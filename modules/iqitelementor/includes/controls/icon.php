<?php
namespace Elementor;

if ( ! defined( 'ELEMENTOR_ABSPATH' ) ) exit; // Exit if accessed directly

class Control_Icon extends Control_Base {

	public function get_type() {
		return 'icon';
	}

	public static function get_icons() {
		return [
			'fa hetj-Cart cbp-mainlink-icon'=>'hej-cart',
			'fa hetj-Delivery cbp-mainlink-icon'=>'hej-Delivery',
			'fa hetj-Hotline cbp-mainlink-icon'=>'hej-Hotline',
			'fa hetj-Lock cbp-mainlink-icon'=>'hej-Lock',
			'fa hetj-Package cbp-mainlink-icon'=>'hej-Package',
			'fa hetj-Satisfied cbp-mainlink-icon'=>'hej-Satisfied',
			'far fa-lock' => 'lock',
			'far fa-smile' => 'smile',
			'far fa-truck' => 'truck',
			'far fa-boxes' => 'boxes',
			'far fa-box' => 'box',
			'far fa-industry-alt' => 'industry-alt',
			'far fa-user-headset' => 'user-headset',
			'fa fa-adjust' => 'adjust',
			'fa fa-adn' => 'adn',
			'fa fa-align-center' => 'align-center',
			'fa fa-align-justify' => 'align-justify',
			'fa fa-align-left' => 'align-left',
			'fa fa-align-right' => 'align-right',
			'fa fa-amazon' => 'amazon',
			'fa fa-ambulance' => 'ambulance',
			'fa fa-american-sign-language-interpreting' => 'american-sign-language-interpreting',
			'fa fa-anchor' => 'anchor',
			'fa fa-android' => 'android',
			'fa fa-angellist' => 'angellist',
			'fa fa-angle-double-down' => 'angle-double-down',
			'fa fa-angle-double-left' => 'angle-double-left',
			'fa fa-angle-double-right' => 'angle-double-right',
			'fa fa-angle-double-up' => 'angle-double-up',
			'fa fa-angle-down' => 'angle-down',
			'fa fa-angle-left' => 'angle-left',
			'fa fa-angle-right' => 'angle-right',
			'fa fa-angle-up' => 'angle-up',
			'fa fa-apple' => 'apple',
			'fa fa-archive' => 'archive',
			'fa fa-area-chart' => 'area-chart',
			'fa fa-arrow-circle-down' => 'arrow-circle-down',
			'fa fa-arrow-circle-left' => 'arrow-circle-left',
			'fa fa-arrow-circle-o-down' => 'arrow-circle-o-down',
			'fa fa-arrow-circle-o-left' => 'arrow-circle-o-left',
			'fa fa-arrow-circle-o-right' => 'arrow-circle-o-right',
			'fa fa-arrow-circle-o-up' => 'arrow-circle-o-up',
			'fa fa-arrow-circle-right' => 'arrow-circle-right',
			'fa fa-arrow-circle-up' => 'arrow-circle-up',
			'fa fa-arrow-down' => 'arrow-down',
			'fa fa-arrow-left' => 'arrow-left',
			'fa fa-arrow-right' => 'arrow-right',
			'fa fa-arrow-up' => 'arrow-up',
			'fa fa-arrows' => 'arrows',
			'fa fa-arrows-alt' => 'arrows-alt',
			'fa fa-arrows-h' => 'arrows-h',
			'fa fa-arrows-v' => 'arrows-v',
			'fa fa-assistive-listening-systems' => 'assistive-listening-systems',
			'fa fa-asterisk' => 'asterisk',
			'fa fa-at' => 'at',
			'fa fa-audio-description' => 'audio-description',
			'fa fa-backward' => 'backward',
			'fa fa-balance-scale' => 'balance-scale',
			'fa fa-ban' => 'ban',
			'fa fa-bar-chart' => 'bar-chart',
			'fa fa-barcode' => 'barcode',
			'fa fa-bars' => 'bars',
			'fa fa-battery-empty' => 'battery-empty',
			'fa fa-battery-full' => 'battery-full',
			'fa fa-battery-half' => 'battery-half',
			'fa fa-battery-quarter' => 'battery-quarter',
			'fa fa-battery-three-quarters' => 'battery-three-quarters',
			'fa fa-bed' => 'bed',
			'fa fa-beer' => 'beer',
			'fa fa-behance' => 'behance',
			'fa fa-behance-square' => 'behance-square',
			'fa fa-bell' => 'bell',
			'fa fa-bell-o' => 'bell-o',
			'fa fa-bell-slash' => 'bell-slash',
			'fa fa-bell-slash-o' => 'bell-slash-o',
			'fa fa-bicycle' => 'bicycle',
			'fa fa-binoculars' => 'binoculars',
			'fa fa-birthday-cake' => 'birthday-cake',
			'fa fa-bitbucket' => 'bitbucket',
			'fa fa-bitbucket-square' => 'bitbucket-square',
			'fa fa-black-tie' => 'black-tie',
			'fa fa-blind' => 'blind',
			'fa fa-bluetooth' => 'bluetooth',
			'fa fa-bluetooth-b' => 'bluetooth-b',
			'fa fa-bold' => 'bold',
			'fa fa-bolt' => 'bolt',
			'fa fa-bomb' => 'bomb',
			'fa fa-book' => 'book',
			'fa fa-bookmark' => 'bookmark',
			'fa fa-bookmark-o' => 'bookmark-o',
			'fa fa-braille' => 'braille',
			'fa fa-briefcase' => 'briefcase',
			'fa fa-btc' => 'btc',
			'fa fa-bug' => 'bug',
			'fa fa-building' => 'building',
			'fa fa-building-o' => 'building-o',
			'fa fa-bullhorn' => 'bullhorn',
			'fa fa-bullseye' => 'bullseye',
			'fa fa-bus' => 'bus',
			'fa fa-buysellads' => 'buysellads',
			'fa fa-calculator' => 'calculator',
			'fa fa-calendar' => 'calendar',
			'fa fa-calendar-check-o' => 'calendar-check-o',
			'fa fa-calendar-minus-o' => 'calendar-minus-o',
			'fa fa-calendar-o' => 'calendar-o',
			'fa fa-calendar-plus-o' => 'calendar-plus-o',
			'fa fa-calendar-times-o' => 'calendar-times-o',
			'fa fa-camera' => 'camera',
			'fa fa-camera-retro' => 'camera-retro',
			'fa fa-car' => 'car',
			'fa fa-caret-down' => 'caret-down',
			'fa fa-caret-left' => 'caret-left',
			'fa fa-caret-right' => 'caret-right',
			'fa fa-caret-square-o-down' => 'caret-square-o-down',
			'fa fa-caret-square-o-left' => 'caret-square-o-left',
			'fa fa-caret-square-o-right' => 'caret-square-o-right',
			'fa fa-caret-square-o-up' => 'caret-square-o-up',
			'fa fa-caret-up' => 'caret-up',
			'fa fa-cart-arrow-down' => 'cart-arrow-down',
			'fa fa-cart-plus' => 'cart-plus',
			'fa fa-cc' => 'cc',
			'fa fa-cc-amex' => 'cc-amex',
			'fa fa-cc-diners-club' => 'cc-diners-club',
			'fa fa-cc-discover' => 'cc-discover',
			'fa fa-cc-jcb' => 'cc-jcb',
			'fa fa-cc-mastercard' => 'cc-mastercard',
			'fa fa-cc-paypal' => 'cc-paypal',
			'fa fa-cc-stripe' => 'cc-stripe',
			'fa fa-cc-visa' => 'cc-visa',
			'fa fa-certificate' => 'certificate',
			'fa fa-chain-broken' => 'chain-broken',
			'fa fa-check' => 'check',
			'fa fa-check-circle' => 'check-circle',
			'fa fa-check-circle-o' => 'check-circle-o',
			'fa fa-check-square' => 'check-square',
			'fa fa-check-square-o' => 'check-square-o',
			'fa fa-chevron-circle-down' => 'chevron-circle-down',
			'fa fa-chevron-circle-left' => 'chevron-circle-left',
			'fa fa-chevron-circle-right' => 'chevron-circle-right',
			'fa fa-chevron-circle-up' => 'chevron-circle-up',
			'fa fa-chevron-down' => 'chevron-down',
			'fa fa-chevron-left' => 'chevron-left',
			'fa fa-chevron-right' => 'chevron-right',
			'fa fa-chevron-up' => 'chevron-up',
			'fa fa-child' => 'child',
			'fa fa-chrome' => 'chrome',
			'fa fa-circle' => 'circle',
			'fa fa-circle-o' => 'circle-o',
			'fa fa-circle-o-notch' => 'circle-o-notch',
			'fa fa-circle-thin' => 'circle-thin',
			'fa fa-clipboard' => 'clipboard',
			'fa fa-clock-o' => 'clock-o',
			'fa fa-clone' => 'clone',
			'fa fa-cloud' => 'cloud',
			'fa fa-cloud-download' => 'cloud-download',
			'fa fa-cloud-upload' => 'cloud-upload',
			'fa fa-code' => 'code',
			'fa fa-code-fork' => 'code-fork',
			'fa fa-codepen' => 'codepen',
			'fa fa-codiepie' => 'codiepie',
			'fa fa-coffee' => 'coffee',
			'fa fa-cog' => 'cog',
			'fa fa-cogs' => 'cogs',
			'fa fa-columns' => 'columns',
			'fa fa-comment' => 'comment',
			'fa fa-comment-o' => 'comment-o',
			'fa fa-commenting' => 'commenting',
			'fa fa-commenting-o' => 'commenting-o',
			'fa fa-comments' => 'comments',
			'fa fa-comments-o' => 'comments-o',
			'fa fa-compass' => 'compass',
			'fa fa-compress' => 'compress',
			'fa fa-connectdevelop' => 'connectdevelop',
			'fa fa-contao' => 'contao',
			'fa fa-copyright' => 'copyright',
			'fa fa-creative-commons' => 'creative-commons',
			'fa fa-credit-card' => 'credit-card',
			'fa fa-credit-card-alt' => 'credit-card-alt',
			'fa fa-crop' => 'crop',
			'fa fa-crosshairs' => 'crosshairs',
			'fa fa-css3' => 'css3',
			'fa fa-cube' => 'cube',
			'fa fa-cubes' => 'cubes',
			'fa fa-cutlery' => 'cutlery',
			'fa fa-dashcube' => 'dashcube',
			'fa fa-database' => 'database',
			'fa fa-deaf' => 'deaf',
			'fa fa-delicious' => 'delicious',
			'fa fa-desktop' => 'desktop',
			'fa fa-deviantart' => 'deviantart',
			'fa fa-diamond' => 'diamond',
			'fa fa-digg' => 'digg',
			'fa fa-dot-circle-o' => 'dot-circle-o',
			'fa fa-download' => 'download',
			'fa fa-dribbble' => 'dribbble',
			'fa fa-dropbox' => 'dropbox',
			'fa fa-drupal' => 'drupal',
			'fa fa-edge' => 'edge',
			'fa fa-eject' => 'eject',
			'fa fa-ellipsis-h' => 'ellipsis-h',
			'fa fa-ellipsis-v' => 'ellipsis-v',
			'fa fa-empire' => 'empire',
			'fa fa-envelope' => 'envelope',
			'fa fa-envelope-o' => 'envelope-o',
			'fa fa-envelope-square' => 'envelope-square',
			'fa fa-envira' => 'envira',
			'fa fa-eraser' => 'eraser',
			'fa fa-eur' => 'eur',
			'fa fa-exchange' => 'exchange',
			'fa fa-exclamation' => 'exclamation',
			'fa fa-exclamation-circle' => 'exclamation-circle',
			'fa fa-exclamation-triangle' => 'exclamation-triangle',
			'fa fa-expand' => 'expand',
			'fa fa-expeditedssl' => 'expeditedssl',
			'fa fa-external-link' => 'external-link',
			'fa fa-external-link-square' => 'external-link-square',
			'fa fa-eye' => 'eye',
			'fa fa-eye-slash' => 'eye-slash',
			'fa fa-eyedropper' => 'eyedropper',
			'fa fa-facebook' => 'facebook',
			'fa fa-facebook-official' => 'facebook-official',
			'fa fa-facebook-square' => 'facebook-square',
			'fa fa-fast-backward' => 'fast-backward',
			'fa fa-fast-forward' => 'fast-forward',
			'fa fa-fax' => 'fax',
			'fa fa-female' => 'female',
			'fa fa-fighter-jet' => 'fighter-jet',
			'fa fa-file' => 'file',
			'fa fa-file-archive-o' => 'file-archive-o',
			'fa fa-file-audio-o' => 'file-audio-o',
			'fa fa-file-code-o' => 'file-code-o',
			'fa fa-file-excel-o' => 'file-excel-o',
			'fa fa-file-image-o' => 'file-image-o',
			'fa fa-file-o' => 'file-o',
			'fa fa-file-pdf-o' => 'file-pdf-o',
			'fa fa-file-powerpoint-o' => 'file-powerpoint-o',
			'fa fa-file-text' => 'file-text',
			'fa fa-file-text-o' => 'file-text-o',
			'fa fa-file-video-o' => 'file-video-o',
			'fa fa-file-word-o' => 'file-word-o',
			'fa fa-files-o' => 'files-o',
			'fa fa-film' => 'film',
			'fa fa-filter' => 'filter',
			'fa fa-fire' => 'fire',
			'fa fa-fire-extinguisher' => 'fire-extinguisher',
			'fa fa-firefox' => 'firefox',
			'fa fa-flag' => 'flag',
			'fa fa-flag-checkered' => 'flag-checkered',
			'fa fa-flag-o' => 'flag-o',
			'fa fa-flask' => 'flask',
			'fa fa-flickr' => 'flickr',
			'fa fa-floppy-o' => 'floppy-o',
			'fa fa-folder' => 'folder',
			'fa fa-folder-o' => 'folder-o',
			'fa fa-folder-open' => 'folder-open',
			'fa fa-folder-open-o' => 'folder-open-o',
			'fa fa-font' => 'font',
			'fa fa-fonticons' => 'fonticons',
			'fa fa-fort-awesome' => 'fort-awesome',
			'fa fa-forumbee' => 'forumbee',
			'fa fa-forward' => 'forward',
			'fa fa-foursquare' => 'foursquare',
			'fa fa-frown-o' => 'frown-o',
			'fa fa-futbol-o' => 'futbol-o',
			'fa fa-gamepad' => 'gamepad',
			'fa fa-gavel' => 'gavel',
			'fa fa-gbp' => 'gbp',
			'fa fa-genderless' => 'genderless',
			'fa fa-get-pocket' => 'get-pocket',
			'fa fa-gg' => 'gg',
			'fa fa-gg-circle' => 'gg-circle',
			'fa fa-gift' => 'gift',
			'fa fa-git' => 'git',
			'fa fa-git-square' => 'git-square',
			'fa fa-github' => 'github',
			'fa fa-github-alt' => 'github-alt',
			'fa fa-github-square' => 'github-square',
			'fa fa-gitlab' => 'gitlab',
			'fa fa-glass' => 'glass',
			'fa fa-glide' => 'glide',
			'fa fa-glide-g' => 'glide-g',
			'fa fa-globe' => 'globe',
			'fa fa-google' => 'google',
			'fa fa-google-plus' => 'google-plus',
			'fa fa-google-plus-square' => 'google-plus-square',
			'fa fa-google-wallet' => 'google-wallet',
			'fa fa-graduation-cap' => 'graduation-cap',
			'fa fa-gratipay' => 'gratipay',
			'fa fa-h-square' => 'h-square',
			'fa fa-hacker-news' => 'hacker-news',
			'fa fa-hand-lizard-o' => 'hand-lizard-o',
			'fa fa-hand-o-down' => 'hand-o-down',
			'fa fa-hand-o-left' => 'hand-o-left',
			'fa fa-hand-o-right' => 'hand-o-right',
			'fa fa-hand-o-up' => 'hand-o-up',
			'fa fa-hand-paper-o' => 'hand-paper-o',
			'fa fa-hand-peace-o' => 'hand-peace-o',
			'fa fa-hand-pointer-o' => 'hand-pointer-o',
			'fa fa-hand-rock-o' => 'hand-rock-o',
			'fa fa-hand-scissors-o' => 'hand-scissors-o',
			'fa fa-hand-spock-o' => 'hand-spock-o',
			'fa fa-hashtag' => 'hashtag',
			'fa fa-hdd-o' => 'hdd-o',
			'fa fa-header' => 'header',
			'fa fa-headphones' => 'headphones',
			'fa fa-heart' => 'heart',
			'fa fa-heart-o' => 'heart-o',
			'fa fa-heartbeat' => 'heartbeat',
			'fa fa-history' => 'history',
			'fa fa-home' => 'home',
			'fa fa-hospital-o' => 'hospital-o',
			'fa fa-hourglass' => 'hourglass',
			'fa fa-hourglass-end' => 'hourglass-end',
			'fa fa-hourglass-half' => 'hourglass-half',
			'fa fa-hourglass-o' => 'hourglass-o',
			'fa fa-hourglass-start' => 'hourglass-start',
			'fa fa-houzz' => 'houzz',
			'fa fa-html5' => 'html5',
			'fa fa-i-cursor' => 'i-cursor',
			'fa fa-ils' => 'ils',
			'fa fa-inbox' => 'inbox',
			'fa fa-indent' => 'indent',
			'fa fa-industry' => 'industry',
			'fa fa-info' => 'info',
			'fa fa-info-circle' => 'info-circle',
			'fa fa-inr' => 'inr',
			'fa fa-instagram' => 'instagram',
			'fa fa-internet-explorer' => 'internet-explorer',
			'fa fa-ioxhost' => 'ioxhost',
			'fa fa-italic' => 'italic',
			'fa fa-joomla' => 'joomla',
			'fa fa-jpy' => 'jpy',
			'fa fa-jsfiddle' => 'jsfiddle',
			'fa fa-key' => 'key',
			'fa fa-keyboard-o' => 'keyboard-o',
			'fa fa-krw' => 'krw',
			'fa fa-language' => 'language',
			'fa fa-laptop' => 'laptop',
			'fa fa-lastfm' => 'lastfm',
			'fa fa-lastfm-square' => 'lastfm-square',
			'fa fa-leaf' => 'leaf',
			'fa fa-leanpub' => 'leanpub',
			'fa fa-lemon-o' => 'lemon-o',
			'fa fa-level-down' => 'level-down',
			'fa fa-level-up' => 'level-up',
			'fa fa-life-ring' => 'life-ring',
			'fa fa-lightbulb-o' => 'lightbulb-o',
			'fa fa-line-chart' => 'line-chart',
			'fa fa-link' => 'link',
			'fa fa-linkedin' => 'linkedin',
			'fa fa-linkedin-square' => 'linkedin-square',
			'fa fa-linux' => 'linux',
			'fa fa-list' => 'list',
			'fa fa-list-alt' => 'list-alt',
			'fa fa-list-ol' => 'list-ol',
			'fa fa-list-ul' => 'list-ul',
			'fa fa-location-arrow' => 'location-arrow',
			'fa fa-lock' => 'lock',
			'fa fa-long-arrow-down' => 'long-arrow-down',
			'fa fa-long-arrow-left' => 'long-arrow-left',
			'fa fa-long-arrow-right' => 'long-arrow-right',
			'fa fa-long-arrow-up' => 'long-arrow-up',
			'fa fa-low-vision' => 'low-vision',
			'fa fa-magic' => 'magic',
			'fa fa-magnet' => 'magnet',
			'fa fa-male' => 'male',
			'fa fa-map' => 'map',
			'fa fa-map-marker' => 'map-marker',
			'fa fa-map-o' => 'map-o',
			'fa fa-map-pin' => 'map-pin',
			'fa fa-map-signs' => 'map-signs',
			'fa fa-mars' => 'mars',
			'fa fa-mars-double' => 'mars-double',
			'fa fa-mars-stroke' => 'mars-stroke',
			'fa fa-mars-stroke-h' => 'mars-stroke-h',
			'fa fa-mars-stroke-v' => 'mars-stroke-v',
			'fa fa-maxcdn' => 'maxcdn',
			'fa fa-meanpath' => 'meanpath',
			'fa fa-medium' => 'medium',
			'fa fa-medkit' => 'medkit',
			'fa fa-meh-o' => 'meh-o',
			'fa fa-mercury' => 'mercury',
			'fa fa-microphone' => 'microphone',
			'fa fa-microphone-slash' => 'microphone-slash',
			'fa fa-minus' => 'minus',
			'fa fa-minus-circle' => 'minus-circle',
			'fa fa-minus-square' => 'minus-square',
			'fa fa-minus-square-o' => 'minus-square-o',
			'fa fa-mixcloud' => 'mixcloud',
			'fa fa-mobile' => 'mobile',
			'fa fa-modx' => 'modx',
			'fa fa-money' => 'money',
			'fa fa-moon-o' => 'moon-o',
			'fa fa-motorcycle' => 'motorcycle',
			'fa fa-mouse-pointer' => 'mouse-pointer',
			'fa fa-music' => 'music',
			'fa fa-neuter' => 'neuter',
			'fa fa-newspaper-o' => 'newspaper-o',
			'fa fa-object-group' => 'object-group',
			'fa fa-object-ungroup' => 'object-ungroup',
			'fa fa-odnoklassniki' => 'odnoklassniki',
			'fa fa-odnoklassniki-square' => 'odnoklassniki-square',
			'fa fa-opencart' => 'opencart',
			'fa fa-openid' => 'openid',
			'fa fa-opera' => 'opera',
			'fa fa-optin-monster' => 'optin-monster',
			'fa fa-outdent' => 'outdent',
			'fa fa-pagelines' => 'pagelines',
			'fa fa-paint-brush' => 'paint-brush',
			'fa fa-paper-plane' => 'paper-plane',
			'fa fa-paper-plane-o' => 'paper-plane-o',
			'fa fa-paperclip' => 'paperclip',
			'fa fa-paragraph' => 'paragraph',
			'fa fa-pause' => 'pause',
			'fa fa-pause-circle' => 'pause-circle',
			'fa fa-pause-circle-o' => 'pause-circle-o',
			'fa fa-paw' => 'paw',
			'fa fa-paypal' => 'paypal',
			'fa fa-pencil' => 'pencil',
			'fa fa-pencil-square' => 'pencil-square',
			'fa fa-pencil-square-o' => 'pencil-square-o',
			'fa fa-percent' => 'percent',
			'fa fa-phone' => 'phone',
			'fa fa-phone-square' => 'phone-square',
			'fa fa-picture-o' => 'picture-o',
			'fa fa-pie-chart' => 'pie-chart',
			'fa fa-pied-piper' => 'pied-piper',
			'fa fa-pied-piper-alt' => 'pied-piper-alt',
			'fa fa-pinterest' => 'pinterest',
			'fa fa-pinterest-p' => 'pinterest-p',
			'fa fa-pinterest-square' => 'pinterest-square',
			'fa fa-plane' => 'plane',
			'fa fa-play' => 'play',
			'fa fa-play-circle' => 'play-circle',
			'fa fa-play-circle-o' => 'play-circle-o',
			'fa fa-plug' => 'plug',
			'fa fa-plus' => 'plus',
			'fa fa-plus-circle' => 'plus-circle',
			'fa fa-plus-square' => 'plus-square',
			'fa fa-plus-square-o' => 'plus-square-o',
			'fa fa-power-off' => 'power-off',
			'fa fa-print' => 'print',
			'fa fa-product-hunt' => 'product-hunt',
			'fa fa-puzzle-piece' => 'puzzle-piece',
			'fa fa-qq' => 'qq',
			'fa fa-qrcode' => 'qrcode',
			'fa fa-question' => 'question',
			'fa fa-question-circle' => 'question-circle',
			'fa fa-question-circle-o' => 'question-circle-o',
			'fa fa-quote-left' => 'quote-left',
			'fa fa-quote-right' => 'quote-right',
			'fa fa-random' => 'random',
			'fa fa-rebel' => 'rebel',
			'fa fa-recycle' => 'recycle',
			'fa fa-reddit' => 'reddit',
			'fa fa-reddit-alien' => 'reddit-alien',
			'fa fa-reddit-square' => 'reddit-square',
			'fa fa-refresh' => 'refresh',
			'fa fa-registered' => 'registered',
			'fa fa-renren' => 'renren',
			'fa fa-repeat' => 'repeat',
			'fa fa-reply' => 'reply',
			'fa fa-reply-all' => 'reply-all',
			'fa fa-retweet' => 'retweet',
			'fa fa-road' => 'road',
			'fa fa-rocket' => 'rocket',
			'fa fa-rss' => 'rss',
			'fa fa-rss-square' => 'rss-square',
			'fa fa-rub' => 'rub',
			'fa fa-safari' => 'safari',
			'fa fa-scissors' => 'scissors',
			'fa fa-scribd' => 'scribd',
			'fa fa-search' => 'search',
			'fa fa-search-minus' => 'search-minus',
			'fa fa-search-plus' => 'search-plus',
			'fa fa-sellsy' => 'sellsy',
			'fa fa-server' => 'server',
			'fa fa-share' => 'share',
			'fa fa-share-alt' => 'share-alt',
			'fa fa-share-alt-square' => 'share-alt-square',
			'fa fa-share-square' => 'share-square',
			'fa fa-share-square-o' => 'share-square-o',
			'fa fa-shield' => 'shield',
			'fa fa-ship' => 'ship',
			'fa fa-shirtsinbulk' => 'shirtsinbulk',
			'fa fa-shopping-bag' => 'shopping-bag',
			'fa fa-shopping-basket' => 'shopping-basket',
			'fa fa-shopping-cart' => 'shopping-cart',
			'fa fa-sign-in' => 'sign-in',
			'fa fa-sign-language' => 'sign-language',
			'fa fa-sign-out' => 'sign-out',
			'fa fa-signal' => 'signal',
			'fa fa-simplybuilt' => 'simplybuilt',
			'fa fa-sitemap' => 'sitemap',
			'fa fa-skyatlas' => 'skyatlas',
			'fa fa-skype' => 'skype',
			'fa fa-slack' => 'slack',
			'fa fa-sliders' => 'sliders',
			'fa fa-slideshare' => 'slideshare',
			'fa fa-smile-o' => 'smile-o',
			'fa fa-snapchat' => 'snapchat',
			'fa fa-snapchat-ghost' => 'snapchat-ghost',
			'fa fa-snapchat-square' => 'snapchat-square',
			'fa fa-sort' => 'sort',
			'fa fa-sort-alpha-asc' => 'sort-alpha-asc',
			'fa fa-sort-alpha-desc' => 'sort-alpha-desc',
			'fa fa-sort-amount-asc' => 'sort-amount-asc',
			'fa fa-sort-amount-desc' => 'sort-amount-desc',
			'fa fa-sort-asc' => 'sort-asc',
			'fa fa-sort-desc' => 'sort-desc',
			'fa fa-sort-numeric-asc' => 'sort-numeric-asc',
			'fa fa-sort-numeric-desc' => 'sort-numeric-desc',
			'fa fa-soundcloud' => 'soundcloud',
			'fa fa-space-shuttle' => 'space-shuttle',
			'fa fa-spinner' => 'spinner',
			'fa fa-spoon' => 'spoon',
			'fa fa-spotify' => 'spotify',
			'fa fa-square' => 'square',
			'fa fa-square-o' => 'square-o',
			'fa fa-stack-exchange' => 'stack-exchange',
			'fa fa-stack-overflow' => 'stack-overflow',
			'fa fa-star' => 'star',
			'fa fa-star-half' => 'star-half',
			'fa fa-star-half-o' => 'star-half-o',
			'fa fa-star-o' => 'star-o',
			'fa fa-steam' => 'steam',
			'fa fa-steam-square' => 'steam-square',
			'fa fa-step-backward' => 'step-backward',
			'fa fa-step-forward' => 'step-forward',
			'fa fa-stethoscope' => 'stethoscope',
			'fa fa-sticky-note' => 'sticky-note',
			'fa fa-sticky-note-o' => 'sticky-note-o',
			'fa fa-stop' => 'stop',
			'fa fa-stop-circle' => 'stop-circle',
			'fa fa-stop-circle-o' => 'stop-circle-o',
			'fa fa-street-view' => 'street-view',
			'fa fa-strikethrough' => 'strikethrough',
			'fa fa-stumbleupon' => 'stumbleupon',
			'fa fa-stumbleupon-circle' => 'stumbleupon-circle',
			'fa fa-subscript' => 'subscript',
			'fa fa-subway' => 'subway',
			'fa fa-suitcase' => 'suitcase',
			'fa fa-sun-o' => 'sun-o',
			'fa fa-superscript' => 'superscript',
			'fa fa-table' => 'table',
			'fa fa-tablet' => 'tablet',
			'fa fa-tachometer' => 'tachometer',
			'fa fa-tag' => 'tag',
			'fa fa-tags' => 'tags',
			'fa fa-tasks' => 'tasks',
			'fa fa-taxi' => 'taxi',
			'fa fa-television' => 'television',
			'fa fa-tencent-weibo' => 'tencent-weibo',
			'fa fa-terminal' => 'terminal',
			'fa fa-text-height' => 'text-height',
			'fa fa-text-width' => 'text-width',
			'fa fa-th' => 'th',
			'fa fa-th-large' => 'th-large',
			'fa fa-th-list' => 'th-list',
			'fa fa-thumb-tack' => 'thumb-tack',
			'fa fa-thumbs-down' => 'thumbs-down',
			'fa fa-thumbs-o-down' => 'thumbs-o-down',
			'fa fa-thumbs-o-up' => 'thumbs-o-up',
			'fa fa-thumbs-up' => 'thumbs-up',
			'fa fa-ticket' => 'ticket',
			'fa fa-times' => 'times',
			'fa fa-times-circle' => 'times-circle',
			'fa fa-times-circle-o' => 'times-circle-o',
			'fa fa-tint' => 'tint',
			'fa fa-toggle-off' => 'toggle-off',
			'fa fa-toggle-on' => 'toggle-on',
			'fa fa-trademark' => 'trademark',
			'fa fa-train' => 'train',
			'fa fa-transgender' => 'transgender',
			'fa fa-transgender-alt' => 'transgender-alt',
			'fa fa-trash' => 'trash',
			'fa fa-trash-o' => 'trash-o',
			'fa fa-tree' => 'tree',
			'fa fa-trello' => 'trello',
			'fa fa-tripadvisor' => 'tripadvisor',
			'fa fa-trophy' => 'trophy',
			'fa fa-truck' => 'truck',
			'fa fa-try' => 'try',
			'fa fa-tty' => 'tty',
			'fa fa-tumblr' => 'tumblr',
			'fa fa-tumblr-square' => 'tumblr-square',
			'fa fa-twitch' => 'twitch',
			'fa fa-twitter' => 'twitter',
			'fa fa-twitter-square' => 'twitter-square',
			'fa fa-umbrella' => 'umbrella',
			'fa fa-underline' => 'underline',
			'fa fa-undo' => 'undo',
			'fa fa-universal-access' => 'universal-access',
			'fa fa-university' => 'university',
			'fa fa-unlock' => 'unlock',
			'fa fa-unlock-alt' => 'unlock-alt',
			'fa fa-upload' => 'upload',
			'fa fa-usb' => 'usb',
			'fa fa-usd' => 'usd',
			'fa fa-user' => 'user',
			'fa fa-user-md' => 'user-md',
			'fa fa-user-plus' => 'user-plus',
			'fa fa-user-secret' => 'user-secret',
			'fa fa-user-times' => 'user-times',
			'fa fa-users' => 'users',
			'fa fa-venus' => 'venus',
			'fa fa-venus-double' => 'venus-double',
			'fa fa-venus-mars' => 'venus-mars',
			'fa fa-viacoin' => 'viacoin',
			'fa fa-viadeo' => 'viadeo',
			'fa fa-viadeo-square' => 'viadeo-square',
			'fa fa-video-camera' => 'video-camera',
			'fa fa-vimeo' => 'vimeo',
			'fa fa-vimeo-square' => 'vimeo-square',
			'fa fa-vine' => 'vine',
			'fa fa-vk' => 'vk',
			'fa fa-volume-control-phone' => 'volume-control-phone',
			'fa fa-volume-down' => 'volume-down',
			'fa fa-volume-off' => 'volume-off',
			'fa fa-volume-up' => 'volume-up',
			'fa fa-weibo' => 'weibo',
			'fa fa-weixin' => 'weixin',
			'fa fa-whatsapp' => 'whatsapp',
			'fa fa-wheelchair' => 'wheelchair',
			'fa fa-wheelchair-alt' => 'wheelchair-alt',
			'fa fa-wifi' => 'wifi',
			'fa fa-wikipedia-w' => 'wikipedia-w',
			'fa fa-windows' => 'windows',
			'fa fa-wordpress' => 'wordpress',
			'fa fa-wpbeginner' => 'wpbeginner',
			'fa fa-wpforms' => 'wpforms',
			'fa fa-wrench' => 'wrench',
			'fa fa-xing' => 'xing',
			'fa fa-xing-square' => 'xing-square',
			'fa fa-y-combinator' => 'y-combinator',
			'fa fa-yahoo' => 'yahoo',
			'fa fa-yelp' => 'yelp',
			'fa fa-youtube' => 'youtube',
			'fa fa-youtube-play' => 'youtube-play',
			'fa fa-youtube-square' => 'youtube-square',
			'fa fa-500px' => '500px',
		];
	}

	protected function get_default_settings() {
		return [
			'icons' => self::get_icons(),
		];
	}

	public function content_template() {
		?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">
				<select class="elementor-control-icon" data-setting="{{ data.name }}" data-placeholder="<?php \IqitElementorWpHelper::_e( 'Select Icon', 'elementor' ); ?>">
					<option value=""><?php \IqitElementorWpHelper::_e( 'Select Icon', 'elementor' ); ?></option>
					<# _.each( data.icons, function( option_title, option_value ) { #>
					<option value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-description">{{ data.description }}</div>
		<# } #>
		<?php
	}
}
