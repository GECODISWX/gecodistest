<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

function upgrade_module_1_4_21()
{
    $sql = array();
    $languages = Language::getLanguages();

    if (!Db::getInstance()->executeS("SHOW TABLES LIKE '" . _DB_PREFIX_ . "lgcookieslaw'")) {
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw` (
            `id_module` int(11) NOT NULL,
            UNIQUE KEY `id_module` (`id_module`)
            ) ENGINE=' . (defined('ENGINE_TYPE') ? ENGINE_TYPE : 'Innodb');
    }

    if (!Db::getInstance()->executeS("SHOW TABLES LIKE '" . _DB_PREFIX_ . "lgcookieslaw_lang'")) {
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lgcookieslaw_lang` (
            `id_lang` int(11) NOT NULL,
            `button1` text NOT NULL,
            `button2` text NOT NULL,
            `content` text NOT NULL,
            `required` text NOT NULL,
            `additional` text NOT NULL,
            UNIQUE KEY `id_lang` (`id_lang`)
            ) ENGINE=' . (defined('ENGINE_TYPE') ? ENGINE_TYPE : 'Innodb') . ' CHARSET=utf8';

        foreach ($languages as $language) {
            switch ($language['iso_code']) {
                case 'en':
                    $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcookieslaw_lang` VALUES (' .
                    (int) $language['id_lang'] . ',
                        "' . pSQL('I accept') . '",
                        "' . pSQL('More information') . '",
                        "' . pSQL(
                            'This website uses its own and third-party cookies to improve our services and show you
                            advertising related to your preferences by analyzing your browsing habits. To give your
                            consent to its use, press the Accept button.',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Necessary to navigate this site and use its functions.</li>
                                <li>Identify you as a user and store your preferences such as language and
                                currency.</li>
                                <li>Customize your experience based on your browsing.</li>
                            </ul>',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Third-party cookies for analytical purposes.</li>
                                <li>Show personalized recommendations based on your browsing on other sites.</li>
                                <li>Show custom campaigns on other websites.</li>
                            </ul>',
                            'html'
                        ) . '"
                        )';
                    break;
                case 'es':
                    $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcookieslaw_lang` VALUES (' .
                    (int) $language['id_lang'] . ',
                        "' . pSQL('Acepto') . '",
                        "' . pSQL('Más información') . '",
                        "' . pSQL(
                            'Este sitio web utiliza cookies propias y de terceros para mejorar nuestros servicios
                            y mostrarle publicidad relacionada con sus preferencias mediante el análisis de sus hábitos
                            de navegación. Para dar su consentimiento sobre su uso pulse el botón Acepto.',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Necesarias para navegar en este sitio y utilizar sus funciones.</li>
                                <li>Identificarle como usuario y almacenar sus preferencias como idioma y moneda.</li>
                                <li>Personalizar su experiencia en base con su navegación.</li>
                            </ul>',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Cookies de terceros con propósitos analíticos.</li>
                                <li>Mostrar recomendaciones personalizadas basadas en su navegación en otros
                                sitios.</li>
                                <li>Mostrar campañas personalizadas en otras sitios web.</li>
                            </ul>',
                            'html'
                        ) . '"
                        )';
                    break;
                case 'fr':
                    $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcookieslaw_lang` VALUES (' .
                    (int) $language['id_lang'] . ',
                        "' . pSQL('J\'accepte') . '",
                        "' . pSQL('Plus d\'informations') . '",
                        "' . pSQL(
                            'Ce site Web utilise ses propres cookies et ceux de tiers pour
                            améliorer nos services et vous montrer des publicités liées à vos
                            préférences en analysant vos habitudes de navigation.
                            Pour donner votre consentement à son utilisation, appuyez sur le bouton Accepter.',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Nécessaire pour naviguer sur ce site et utiliser ses fonctions.</li>
                                <li>Vous identifier en tant qu\'utilisateur et enregistrer vos préférences telles que
                                la langue et la devise.</li>
                                <li>Personnalisez votre expérience en fonction de votre navigation.</li>
                            </ul>',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Cookies tiers à des fins d\'analyse.</li>
                                <li>Afficher des recommandations personnalisées en fonction de votre navigation
                                sur d\'autres sites</li>
                                <li>Afficher des campagnes personnalisées sur d\'autres sites Web</li>
                            </ul>',
                            'html'
                        ) . '"
                        )';
                    break;
                case 'it':
                    $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcookieslaw_lang` VALUES (' .
                    (int) $language['id_lang'] . ',
                        "' . pSQL('Accetto') . '",
                        "' . pSQL('Piú info') . '",
                        "' . pSQL(
                            'Questo sito web utilizza cookie propri e di terze parti per migliorare i
                            nostri servizi e mostrarti pubblicità relativa alle tue preferenze analizzando
                            le tue abitudini di navigazione. Per dare il tuo consenso al suo utilizzo,
                            premi il pulsante Accetto.',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Necessario per navigare in questo sito e utilizzare le sue funzioni.</li>
                                <li>Identificarti come utente e memorizzare le tue preferenze come lingua e valuta.</li>
                                <li>Personalizza la tua esperienza in base alla tua navigazione.</li>
                            </ul>',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Cookie di terze parti per scopi analitici.</li>
                                <li>Mostra consigli personalizzati basati sulla tua navigazione su altri siti.</li>
                                <li>Mostra campagne personalizzate su altri siti web.</li>
                            </ul>',
                            'html'
                        ) . '"
                        )';
                    break;
                case 'de':
                    $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcookieslaw_lang` VALUES (' .
                    (int) $language['id_lang'] . ',
                        "' . pSQL('Ich akzeptiere') . '",
                        "' . pSQL('Weitere Informationen') . '",
                        "' . pSQL(
                            'Diese Website verwendet eigene Cookies und Cookies von Drittanbietern, um unsere Dienste zu
                            verbessern. Und zeigen Sie Werbung in Bezug auf Ihre Vorlieben, indem Sie Ihre Gewohnheiten
                            analysieren navigation. Um Ihre Zustimmung zu seiner Verwendung zu geben, klicken Sie auf
                            die Schaltfläche Akzeptieren.',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Erforderlich, um auf dieser Site zu navigieren und ihre Funktionen zu nutzen.</li>
                                <li>Identifizieren Sie sich als Benutzer und speichern Sie Ihre Einstellungen wie
                                Sprache und Währung.</li>
                                <li>Passen Sie Ihre Erfahrung basierend auf Ihrem Surfen an.</li>
                            </ul>',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Cookies von Drittanbietern zu Analysezwecken.</li>
                                <li>Zeigen Sie personalisierte Empfehlungen basierend auf Ihrem Surfen auf anderen
                                Websites an.</li>
                                <li>Benutzerdefinierte Kampagnen auf anderen Websites anzeigen.</li>
                            </ul>',
                            'html'
                        ) . '"
                        )';
                    break;
                case 'pt':
                    $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcookieslaw_lang` VALUES (' .
                    (int) $language['id_lang'] . ',
                        "' . pSQL('Aceito') . '",
                        "' . pSQL('Mais informações') . '",
                        "' . pSQL(
                            'Este site usa cookies próprios e de terceiros para melhorar nossos serviços
                            e mostrar a publicidade relacionada às suas preferências, analisando seus hábitos
                            navegação. Para dar seu consentimento ao seu uso, pressione o botão Aceito.',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Necessário para navegar neste site e usar suas funções.</li>
                                <li>Identifique você como um usuário e armazene suas preferências, como idioma e moeda.
                                </li>
                                <li>Personalize sua experiência com base em sua navegação.</li>
                            </ul>',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Cookies de terceiros para fins analíticos.</li>
                                <li>Mostre recomendações personalizadas com base na sua navegação em outros sites.</li>
                                <li>Mostre campanhas personalizadas em outros sites.</li>
                            </ul>',
                            'html'
                        ) . '"
                        )';
                    break;
                case 'pl':
                    $sql[] = 'INSERT INTO `' . _DB_PREFIX_ . 'lgcookieslaw_lang` VALUES (' .
                    (int) $language['id_lang'] . ',
                        "' . pSQL('Akceptuję') . '",
                        "' . pSQL('Więcej informacji') . '",
                        "' . pSQL(
                            'Ta witryna korzysta z własnych plików cookie i plików cookie stron trzecich w celu
                            ulepszenia naszych usług i pokazywać Ci reklamy związane z Twoimi preferencjami, analizując
                            Twoje nawyki nawigacja. Aby wyrazić zgodę na jego użycie, naciśnij przycisk Akceptuj.',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Konieczne do poruszania się po tej witrynie i korzystania z jej funkcji.</li>
                                <li>Zidentyfikować Cię jako użytkownika i zapisać swoje ustawienia, takie jak język i
                                waluta.</li>
                                <li>Dostosuj sposób działania na podstawie sposobu przeglądania. </li>
                            </ul>',
                            'html'
                        ) . '",
                        "' . pSQL(
                            '<ul>
                                <li>Pliki cookie innych firm do celów analitycznych.</li>
                                <li>Pokaż spersonalizowane rekomendacje na podstawie tego, co przeglądasz w innych
                                witrynach.</li>
                                <li>Wyświetlaj kampanie niestandardowe w innych witrynach.</li>
                            </ul>',
                            'html'
                        ) . '"
                        )';
                    break;
            }
        }
    } else {
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` ADD `required` TEXT NOT NULL';
        $sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` ADD `additional` TEXT NOT NULL';

        foreach ($languages as $language) {
            switch ($language['iso_code']) {
                case 'en':
                    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` SET
                        `content` = "' . pSQL(
                        'This website uses its own and third-party cookies to improve our services and show you
                            advertising related to your preferences by analyzing your browsing habits. To give your
                            consent to its use, press the Accept button.',
                        'html'
                    ) . '",
                        `required` = "' . pSQL(
                        '<ul>
                            <li>Necessary to navigate this site and use its functions.</li>
                            <li>Identify you as a user and store your preferences such as language and currency.
                            </li>
                            <li>Customize your experience based on your browsing.</li>
                        </ul>',
                        'html'
                    ) . '",
                        `additional` = "' . pSQL(
                        '<ul>
                            <li>Third-party cookies for analytical purposes.</li>
                            <li>Show personalized recommendations based on your browsing on other sites.</li>
                            <li>Show custom campaigns on other websites.</li>
                        </ul>',
                        'html'
                    ) . '"
                    WHERE `id_lang` = ' . (int) $language['id_lang'];
                    break;
                case 'es':
                    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` SET
                        `content` = "' . pSQL(
                        'Este sitio web utiliza cookies propias y de terceros para mejorar nuestros servicios
                            y mostrarle publicidad relacionada con sus preferencias mediante el análisis de sus hábitos
                            de navegación. Para dar su consentimiento sobre su uso pulse el botón Acepto.',
                        'html'
                    ) . '",
                        `required` = "' . pSQL(
                        '<ul>
                            <li>Necesarias para navegar en este sitio y utilizar sus funciones.</li>
                            <li>Identificarle como usuario y almacenar sus preferencias como idioma y moneda.</li>
                            <li>Personalizar su experiencia en base con su navegación.</li>
                        </ul>',
                        'html'
                    ) . '",
                        `additional` = "' . pSQL(
                        '<ul>
                            <li>Cookies de terceros con propósitos analíticos.</li>
                            <li>Mostrar recomendaciones personalizadas basadas en su navegación en otros sitios.
                            </li>
                            <li>Mostrar campañas personalizadas en otras sitios web.</li>
                        </ul>',
                        'html'
                    ) . '"
                    WHERE `id_lang` = ' . (int) $language['id_lang'];
                    break;
                case 'fr':
                    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` SET
                        `content` = "' . pSQL(
                        'Ce site Web utilise ses propres cookies et ceux de tiers pour
                            améliorer nos services et vous montrer des publicités liées à vos
                            préférences en analysant vos habitudes de navigation.
                            Pour donner votre consentement à son utilisation, appuyez sur le bouton Accepter.',
                        'html'
                    ) . '",
                        `required` = "' . pSQL(
                        '<ul>
                            <li>Nécessaire pour naviguer sur ce site et utiliser ses fonctions.</li>
                            <li>Vous identifier en tant qu\'utilisateur et enregistrer vos préférences telles que
                            la langue et la devise.</li>
                            <li>Personnalisez votre expérience en fonction de votre navigation.</li>
                        </ul>',
                        'html'
                    ) . '",
                        `additional` = "' . pSQL(
                        '<ul>
                            <li>Cookies tiers à des fins d\'analyse.</li>
                            <li>Afficher des recommandations personnalisées en fonction de votre navigation
                            sur d\'autres sites</li>
                            <li>Afficher des campagnes personnalisées sur d\'autres sites Web</li>
                        </ul>',
                        'html'
                    ) . '"
                    WHERE `id_lang` = ' . (int) $language['id_lang'];
                    break;
                case 'it':
                    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` SET
                        `content` = "' . pSQL(
                        'Questo sito web utilizza cookie propri e di terze parti per migliorare i
                            nostri servizi e mostrarti pubblicità relativa alle tue preferenze analizzando
                            le tue abitudini di navigazione. Per dare il tuo consenso al suo utilizzo,
                            premi il pulsante Accetto.',
                        'html'
                    ) . '",
                        `required` = "' . pSQL(
                        '<ul>
                            <li>Necessario per navigare in questo sito e utilizzare le sue funzioni.</li>
                            <li>Identificarti come utente e memorizzare le tue preferenze come lingua e valuta.</li>
                            <li>Personalizza la tua esperienza in base alla tua navigazione.</li>
                        </ul>',
                        'html'
                    ) . '",
                        `additional` = "' . pSQL(
                        '<ul>
                            <li>Cookie di terze parti per scopi analitici.</li>
                            <li>Mostra consigli personalizzati basati sulla tua navigazione su altri siti.</li>
                            <li>Mostra campagne personalizzate su altri siti web.</li>
                        </ul>',
                        'html'
                    ) . '"
                    WHERE `id_lang` = ' . (int) $language['id_lang'];
                    break;
                case 'de':
                    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` SET
                        `content` = "' . pSQL(
                        'Diese Website verwendet eigene Cookies und Cookies von Drittanbietern, um unsere Dienste zu
                            verbessern. Und zeigen Sie Werbung in Bezug auf Ihre Vorlieben, indem Sie Ihre Gewohnheiten
                            analysieren navigation. Um Ihre Zustimmung zu seiner Verwendung zu geben, klicken Sie auf
                            die Schaltfläche Akzeptieren.',
                        'html'
                    ) . '",
                        `required` = "' . pSQL(
                        '<ul>
                            <li>Erforderlich, um auf dieser Site zu navigieren und ihre Funktionen zu nutzen.</li>
                            <li>Identifizieren Sie sich als Benutzer und speichern Sie Ihre Einstellungen wie
                            Sprache und Währung.</li>
                            <li>Passen Sie Ihre Erfahrung basierend auf Ihrem Surfen an.</li>
                        </ul>',
                        'html'
                    ) . '",
                        `additional` = "' . pSQL(
                        '<ul>
                            <li>Cookies von Drittanbietern zu Analysezwecken.</li>
                            <li>Zeigen Sie personalisierte Empfehlungen basierend auf Ihrem Surfen auf anderen
                            Websites an.</li>
                            <li>Benutzerdefinierte Kampagnen auf anderen Websites anzeigen.</li>
                        </ul>',
                        'html'
                    ) . '"
                    WHERE `id_lang` = ' . (int) $language['id_lang'];
                    break;
                case 'pt':
                    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` SET
                        `content` = "' . pSQL(
                        'Este site usa cookies próprios e de terceiros para melhorar nossos serviços
                            e mostrar a publicidade relacionada às suas preferências, analisando seus hábitos
                            navegação. Para dar seu consentimento ao seu uso, pressione o botão Aceito.',
                        'html'
                    ) . '",
                        `required` = "' . pSQL(
                        '<ul>
                            <li>Necessário para navegar neste site e usar suas funções.</li>
                            <li>Identifique você como um usuário e armazene suas preferências, como idioma e moeda.
                            </li>
                            <li>Personalize sua experiência com base em sua navegação.</li>
                        </ul>',
                        'html'
                    ) . '",
                        `additional` = "' . pSQL(
                        '<ul>
                            <li>Cookies de terceiros para fins analíticos.</li>
                            <li>Mostre recomendações personalizadas com base na sua navegação em outros sites.</li>
                            <li>Mostre campanhas personalizadas em outros sites.</li>
                        </ul>',
                        'html'
                    ) . '"
                    WHERE `id_lang` = ' . (int) $language['id_lang'];
                    break;
                case 'pl':
                    $sql[] = 'UPDATE `' . _DB_PREFIX_ . 'lgcookieslaw_lang` SET
                        `content` = "' . pSQL(
                        'Ta witryna korzysta z własnych plików cookie i plików cookie stron trzecich w celu
                            ulepszenia naszych usług i pokazywać Ci reklamy związane z Twoimi preferencjami, analizując
                            Twoje nawyki nawigacja. Aby wyrazić zgodę na jego użycie, naciśnij przycisk Akceptuj.',
                        'html'
                    ) . '",
                        `required` = "' . pSQL(
                        '<ul>
                            <li>Konieczne do poruszania się po tej witrynie i korzystania z jej funkcji.</li>
                            <li>Zidentyfikować Cię jako użytkownika i zapisać swoje ustawienia, takie jak język i
                            waluta.</li>
                            <li>Dostosuj sposób działania na podstawie sposobu przeglądania. </li>
                        </ul>',
                        'html'
                    ) . '",
                        `additional` = "' . pSQL(
                        '<ul>
                            <li>Pliki cookie innych firm do celów analitycznych.</li>
                            <li>Pokaż spersonalizowane rekomendacje na podstawie tego, co przeglądasz w innych
                            witrynach.</li>
                            <li>Wyświetlaj kampanie niestandardowe w innych witrynach.</li>
                        </ul>',
                        'html'
                    ) . '"
                    WHERE `id_lang` = ' . (int) $language['id_lang'];
                    break;
            }
        }
    }

    foreach ($sql as $q) {
        Db::getInstance()->execute($q);
    }

    return true;
}
