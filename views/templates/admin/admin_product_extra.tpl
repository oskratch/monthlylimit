<div class="panel" id="fieldset_0">
        <div style="margin-top:30px;">											
            <div class="form-group">
                <label for="quantity">
                    {l s='Cantidad máxima de unidades que un cliente puede comprar en un mes'}
                </label>
                <div class="col-lg-8">
                    <input type="number" id="quantity" name="quantity" min="0" max="999" value="{if isset($monthlyLimitProducts) && is_numeric($monthlyLimitProducts)}{$monthlyLimitProducts}{else}0{/if}" />
                </div>
            </div>										
            <div class="form-group">
                <small>* {l s='Para deshabilitar el límite, ingresar 0'}</small>
            </div>
        </div>
    </div>
</div>
