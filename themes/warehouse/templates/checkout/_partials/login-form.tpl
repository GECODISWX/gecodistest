{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{extends file='customer/_partials/login-form.tpl'}

{block name='login_form_start'}
<div class="collapse show" id="personal-information-step-login">
{/block}

{block name='form_buttons'}
  <button
    class="continue btn btn-primary btn-block btn-lg"
    name="continue"
    data-link-action="sign-in"
    type="submit"
    value="1"
  >
    {l s='Sign in' d='Shop.Theme.Actions'}
  </button>
{/block}

  {block name='login_form_end'}
</div>
    {/block}
