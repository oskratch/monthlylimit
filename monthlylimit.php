<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_.'monthlylimit/classes/LimitManager.php');

class MonthlyLimit extends Module {

    public $tabs = [
        [
            'name' => 'Límites mensuales',
            'class_name' => 'AdminLimitOrders',
            'parent_class_name' => 'AdminParentOrders'
        ],
    ];

    public function __construct(){
        $this->name = 'monthlylimit';
        $this->tab = 'administration';
        $this->version = '1.0.1';
        $this->author = 'Oscar Periche - 4funkies';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Monthly Limit'); 
        $this->description = $this->l('Este módulo permite controlar las compras mensuales de los empleados en la tienda estableciendo límites específicos. Define un importe máximo de compra mensual, el número máximo de veces que un empleado puede comprar cada mes o la cantidad total de productos que cada empleado puede adquirir mensualmente.');
    }

    public function install(){
        if (!parent::install() || !$this->installTab()) {
            return false;
        }
        
        Configuration::updateValue('MONTHLY_LIMIT_EUROS', 0); // Limit purchase amount - 0 unlimited
        Configuration::updateValue('MONTHLY_LIMIT_TIMES', 0); // Limit times - 0 unlimited
        Configuration::updateValue('MONTHLY_LIMIT_PRODUCTS', 0); // Limit products - 0 unlimited

        if (!$this->registerHook('actionCartUpdateQuantityBefore')) {
            return false;
        }

        // Copy custom.js to theme to override default behaviour
        $activeTheme = Configuration::get('PS_THEME_NAME');
        $source = _PS_MODULE_DIR_.'monthlylimit/assets/js/custom.js';
        $destination = _PS_THEME_DIR_.$activeTheme.'/assets/js/custom.js';
        copy($source, $destination);

        return true;
    }

    public function uninstall(){
        if (!parent::uninstall() || !$this->uninstallTab()) {
            return false;
        }

        // Delete custom.js from theme
        $activeTheme = Configuration::get('PS_THEME_NAME');
        $destination = _PS_THEME_DIR_.$activeTheme.'/assets/js/custom.js';
        if (file_exists($destination)) {
            unlink($destination);
        }

        return true;
    }

    public function enable($force_all = false) {
        return parent::enable($force_all)
            && $this->installTab()
        ;
    }

    public function disable($force_all = false) {
        return parent::disable($force_all)
            && $this->uninstallTab()
        ;
    }

    public function installTab() {
        $tabId = (int) Tab::getIdFromClassName('AdminLimitOrdersController');
    
        if (!$tabId) {
            $tab = new Tab();
        } else {
            $tab = new Tab($tabId);
        }

        $tab->active = 1;
        $tab->class_name = 'AdminLimitOrdersController'; 
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans('Pendientes de aprobación', array(), 'Modules.MonthlyLimit.Admin', $lang['locale']);
        }
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentOrders'); 
        $tab->module = $this->name;

        return $tab->save();

    }  

    public function uninstallTab() {
        $tabId = (int) Tab::getIdFromClassName('AdminLimitOrdersController');

        if ($tabId) {
            $tab = new Tab($tabId);
            $tab->delete();
        }

        return true;

    }

    /*
    // Hooks
    */

    public function hookActionCartUpdateQuantityBefore($params) {
        
        $cart = $params['cart'];
        $productId = $params['product']->id;
        $quantity = $params['quantity'];
        $operator = $params['operator'];
        $customerId = (int) $cart->id_customer;
        
        $limitManager = new LimitManager();
        
        $errorMessages = [];
        
        // Comprovar límit d'euros
        $errorEuros = $limitManager->checkMonthlyLimitEuros($cart, $customerId, $productId, $quantity, $operator); 
        if ($errorEuros) {
            $errorMessages[] = $errorEuros;
        }
        
        // Comprovar límit de productes per ID
        $errorProducts = $limitManager->checkMonthlyLimitProducts($cart, $customerId, $productId, $quantity, $operator);
        if ($errorProducts) {
            $errorMessages[] = $errorProducts;
        }
        
        // Comprovar límit de comandes
        $errorTimes = $limitManager->checkMonthlyLimitTimes($customerId);
        if ($errorTimes) {
            $errorMessages[] = $errorTimes;
        }
        
        if (!empty($errorMessages)) {
            $fullErrorMessage = implode("\n", $errorMessages);
        
            $errorMessage = [
                'hasError' => true,
                'errors' => [$fullErrorMessage]
            ];
        
            die(json_encode($errorMessage));
            
        } else {
            return true;
        }
    }
    
}
