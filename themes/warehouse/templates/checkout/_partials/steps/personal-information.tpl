{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  {if $customer.is_logged && !$customer.is_guest}

    <p class="identity">
      {* [1][/1] is for a HTML tag. *}
      {l s='Connected as [1]%firstname% %lastname%[/1].'
        d='Shop.Theme.Customeraccount'
        sprintf=[
          '[1]' => "<a href='{$urls.pages.identity}'><u>",
          '[/1]' => "</u></a>",
          '%firstname%' => $customer.firstname,
          '%lastname%' => $customer.lastname
        ]
      }
    </p>
    <p>
      {* [1][/1] is for a HTML tag. *}
      {l
        s='Not you? [1]Log out[/1]'
        d='Shop.Theme.Customeraccount'
        sprintf=[
        '[1]' => "<a href='{$urls.actions.logout}'><u>",
        '[/1]' => "</u></a>"
        ]
      }
    </p>
    {if !isset($empty_cart_on_logout) || $empty_cart_on_logout}
      <p><small>{l s='If you sign out now, your cart will be emptied.' d='Shop.Theme.Checkout'}</small></p>
    {/if}


    <div class="clearfix">
      <form method="GET" action="{$urls.pages.order}">
        <button
                class="continue btn btn-primary   btn-block btn-lg"
                name="controller"
                type="submit"
                value="order"
        >
          {l s='Continue' d='Shop.Theme.Actions'}
        </button>
      </form>

    </div>

  {else}


    <p class="">  <strong>
      <i class="fa fa-question-circle-o" aria-hidden="true"></i> {l s='Already have an account?' d='Shop.Theme.Customeraccount'}
      <a  href="#personal-information-step-login" >
        {l s='Log in instead!' d='Shop.Theme.Customeraccount'} <i class="fa fa-angle-right" aria-hidden="true"></i>
      </a></strong>
    </p>

    {render file='checkout/_partials/login-form.tpl' ui=$login_form}

    {block name='display_after_login_form'}
      {hook h='displayCheckoutLoginFormAfter'}
    {/block}
    <div class="no-account">
      <strong><a href="{$urls.pages.register}" data-link-action="display-register-form">
        {l s='No account? Create one here' d='Shop.Theme.Customeraccount'} <i class="fa fa-angle-right" aria-hidden="true"></i>
      </a></strong>
    </div>
    {render file='checkout/_partials/customer-form.tpl' ui=$register_form guest_allowed=$guest_allowed}


  {/if}
{/block}
