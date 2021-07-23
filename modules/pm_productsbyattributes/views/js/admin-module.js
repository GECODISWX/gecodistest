var currentColorPicker = false;
$(document).ready(function() {
	$('div#addons-rating-container p.dismiss a').click(function() {
		$('div#addons-rating-container').hide(500);
		$.ajax({type : "GET", url : window.location+'&dismissRating=1' });
		return false;
	});

    $('a.list-group-item').click(function() {
        $('a.list-group-item').removeClass('active');
        $(this).addClass('active');
        $('input[name=selected_tab]').val($(this).data('identifier'));
    });

    handleSelectedGroupOption();
    handleChangeProductNameOption();

    $('select[name="selectedGroups[]"]').selectize({
        plugins: ['drag_drop', 'remove_button'],
        delimiter: ',',
        persist: false,
        onChange: function(value) {
            addPositionToSelectizeItems(this);
        },
        onInitialize: function() {
            addPositionToSelectizeItems(this);
        }
    });
});

function addPositionToSelectizeItems(selectizeItem)
{
    let originalOptions = selectizeItem.options;
    $('div.item', selectizeItem.$control).each(function(index, item) {
        let originalOption = originalOptions[$(this).data('value')];
        let currentPosition = (index + 1);
        let itemLinks = $('a', item).detach();
        $(item).html(currentPosition + '.&nbsp;' + originalOption.text);
        $(item).append(itemLinks);
    });
}

function initTips(e) {
	$(document).ready(function() { $(e+"-tips").tipTip(); });
}

function handleChangeProductNameOption() {
    var val = parseInt($('input[name="changeProductName"]:checked').val());

    if (!val) {
        $('.nameSeparatorFormGroup').hide();
    } else {
        $('.nameSeparatorFormGroup').show();
    }
}

function handleSelectedGroupOption() {
    let val = $('select[name="selectedGroups[]"]').val();

    if (typeof(val) == 'undefined' || val == null || !val.length) {
        $('.more_options').addClass('pm_hide');
        $('.form-group').addClass('hide');
        $('.form-group.selectedGroups').removeClass('hide');
        $('.panel.maintenance .form-group').removeClass('hide');
        $('.panel.showPagesOption').addClass('hide');
        $('a.list-group-item[data-identifier=backward]').addClass('hide');
    } else {
        $('.more_options').removeClass('pm_hide');
        $('.form-group').removeClass('hide');
        $('.panel.showPagesOption').removeClass('hide');
        $('a.list-group-item[data-identifier=backward]').removeClass('hide');
        if (typeof color_groups !== 'undefined') {
            display_hide_squares_color();
        }
    }
}

function display_hide_squares_color() {
    let selectedGroups = $('select[name="selectedGroups[]"]').val();
    let hasSelectedColorGroup = false;
    for (var i = selectedGroups.length - 1; i >= 0; i--) {
        if ($.inArray(parseInt(selectedGroups[i]), color_groups) != -1) {
            hasSelectedColorGroup = true;
            break;
        }
    }

    if (hasSelectedColorGroup) {
        $('.colorSquaresFormGroup').show();
    } else {
        $('.colorSquaresFormGroup').hide();

        $('#hideColorSquares_on').prop('checked', false);
        $('#hideColorSquares_off').prop('checked', true);
    }
}