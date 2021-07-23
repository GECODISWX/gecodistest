{*
*
* Products by Attributes
*
* @author Presta-Module.com <support@presta-module.com>
* @copyright Presta-Module
*
*           ____     __  __
*          |  _ \   |  \/  |
*          | |_) |  | |\/| |
*          |  __/   | |  | |
*          |_|      |_|  |_|
*
*}

<div class="panel pm_fieldset">
    <div class="panel-heading">
        <h2>{l s='New version of the module' mod='pm_productsbyattributes'}</h2>
    </div>
    <div class="form-wrapper">
	    <div class="alert warning">
			<ul class="list-unstyled">
				<li>
					<p>{l s='We have detected that you installed a new version of the module on your shop' mod='pm_productsbyattributes'}</p>
				</li>
			</ul>
		</div>

		<div class="text-center">
			<a href="{$_base_config_url|escape:'html':'UTF-8'}&makeUpdate=1" class="button">
				{l s='Please click here in order to finish the installation process' mod='pm_productsbyattributes'}
			</a>
		</div>
	</div>
</div>