<div class="alert alert-info">
    <img src="../modules/monthlylimit/logo.png" style="float:left; margin-right:15px;" height="60">
    <p><strong>Información importante.</strong></p>
    <p>{l s='Si deseas deshabilitar un límite específico, ingresa el número 0 en el campo correspondiente y se desactivará.'}</p>
</div>
<form action="{$link->getAdminLink('AdminLimitOrders')}" class="defaultForm form-horizontal" method="post">    
    <input type="hidden" name="btnSubmit" value="1" />				
    <div class="panel" id="fieldset_0">												
        <div class="panel-heading">
            <i class="icon-envelope"></i>							
            Establece límites en las compras
        </div>
        <div class="form-wrapper">											
            <div class="form-group">
                <label class="control-label col-lg-4 required">
                    {l s='Límite en euros'}
                </label>
                <div class="col-lg-8">
                    <input type="text" name="monthly_limit_euros" id="monthly_limit_euros" value="{$monthly_limit_euros}" required />
                </div>
            </div>										
            <div class="form-group">
                <label class="control-label col-lg-4 required">
                    {l s='Límite de compras'}
                </label>
                <div class="col-lg-8">
                    <input type="text" name="monthly_limit_times" id="monthly_limit_times" value="{$monthly_limit_times}" required />
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <button type="submit" value="1"	id="configuration_form_submit_btn" name="btnSubmit" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> Guardar
            </button>
        </div>
    </div>
    <input type="hidden" name="_token" value="{$_token}" />
</form>