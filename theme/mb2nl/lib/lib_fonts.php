<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package   theme_mb2nl
 * @copyright 2017 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license   PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 *
 */


/**
 *
 * Method to check Goole webfonts are in use
 *
 */
function theme_mb2nl_is_gfonts() {
    global $PAGE;

    $gfontsettings = theme_mb2nl_theme_setting($PAGE, 'ffgeneral') . theme_mb2nl_theme_setting($PAGE, 'ffheadings') .
    theme_mb2nl_theme_setting($PAGE, 'ffmenu') . theme_mb2nl_theme_setting($PAGE, 'ffddmenu');

    $cache = cache::make('theme_mb2nl', 'features');
    $cacheid = 'gfont_' . $gfontsettings;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    if (preg_match('@gfont@', $gfontsettings)) {
        $cache->set($cacheid, 1);
        return 1;
    }

    $cache->set($cacheid, 0);
    return 0;

}


/**
 *
 * Method to get Google webfonts array
 *
 */
function theme_mb2nl_gfonts_arr() {

    global $PAGE;

    $gfonts = [];
    $gfontsettings = theme_mb2nl_theme_setting($PAGE, 'ffgeneral') . theme_mb2nl_theme_setting($PAGE, 'ffheadings') .
    theme_mb2nl_theme_setting($PAGE, 'ffmenu') . theme_mb2nl_theme_setting($PAGE, 'ffddmenu');

    $cache = cache::make('theme_mb2nl', 'features');
    $cacheid = 'gfont_arr_' . theme_mb2nl_string_url_safe($gfontsettings . theme_mb2nl_theme_setting($PAGE, 'gfont1') .
    theme_mb2nl_theme_setting($PAGE, 'gfont2') . theme_mb2nl_theme_setting($PAGE, 'gfont3'));

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    for ($i = 1; $i <= 3; $i++) {
        $gfontname = theme_mb2nl_theme_setting($PAGE, 'gfont' . $i);
        $gfontstyle = theme_mb2nl_theme_setting($PAGE, 'gfontstyle' . $i);
        $gfontstyle = str_replace(' ', '', $gfontstyle);
        $gfontstylearr = explode(',', $gfontstyle);
        $fwend = count($gfontstylearr) > 1 ? '..' . end($gfontstylearr) : '';
        $optszfonts = theme_mb2nl_opsz_gfonts();
        $variablefonts = theme_mb2nl_variable_gfonts();
        $optszattr = '';
        $optszval = '';

        if (!$gfontname) {
            continue;
        }

        if (in_array($gfontname, $variablefonts)) {
            $fwval = array_shift($gfontstylearr) . $fwend;
        } else {
            $fwval = str_replace(',', ';', $gfontstyle);
        }

        if (array_key_exists($gfontname, $optszfonts)) {
            $optszattr = 'opsz,';
            $optszval = $optszfonts[$gfontname]['min'] . '..' . $optszfonts[$gfontname]['max'] . ',';
        }

        $gfonts[] = [
            'set' => preg_match('@gfont' . $i . '@', $gfontsettings),
            'name' => str_replace(' ', '+', $gfontname),
            'style' => $optszattr . 'wght@' . $optszval . $fwval,
        ];
    }

    $cache->set($cacheid, $gfonts);
    return $gfonts;

}




/**
 *
 * Method to get Google webfonts with the "Optical size" attributes.
 *
 */
function theme_mb2nl_opsz_gfonts() {

    return [
        'Ballet' => ['def' => 16, 'min' => 16, 'max' => 72],
        'Big Shoulders' => ['def' => 14, 'min' => 10, 'max' => 72],
        'Big Shoulders Inline' => ['def' => 14, 'min' => 10, 'max' => 72],
        'Big Shoulders Stencil' => ['def' => 14, 'min' => 10, 'max' => 72],
        'Bodoni Moda' => ['def' => 11, 'min' => 6, 'max' => 96],
        'Bodoni Moda SC' => ['def' => 11, 'min' => 6, 'max' => 96],
        'Bricolage Grotesque' => ['def' => 14, 'min' => 12, 'max' => 96],
        'DM Sans' => ['def' => 14, 'min' => 9, 'max' => 40],
        'Fraunces' => ['def' => 14, 'min' => 9, 'max' => 144],
        'Hedvig Letters Serif' => ['def' => 24, 'min' => 12, 'max' => 24],
        'Imbue' => ['def' => 10, 'min' => 10, 'max' => 100],
        'Jaro' => ['def' => 14, 'min' => 6, 'max' => 72],
        'Literata' => ['def' => 14, 'min' => 7, 'max' => 72],
        'Merriweather' => ['def' => 18, 'min' => 18, 'max' => 144],
        'Montagu Slab' => ['def' => 144, 'min' => 16, 'max' => 144],
        'Newsreader' => ['def' => 16, 'min' => 6, 'max' => 72],
        'Nunito Sans' => ['def' => 12, 'min' => 6, 'max' => 12],
        'Pathway Extreme' => ['def' => 12, 'min' => 8, 'max' => 144],
        'Piazzolla' => ['def' => 14, 'min' => 8, 'max' => 30],
        'Playfair' => ['def' => 14, 'min' => 5, 'max' => 1200],
        'Roboto Flex' => ['def' => 14, 'min' => 8, 'max' => 144],
        'Roboto Serif' => ['def' => 14, 'min' => 8, 'max' => 144],
        'Source Serif 4' => ['def' => 14, 'min' => 8, 'max' => 60],
        'Texturina' => ['def' => 12, 'min' => 12, 'max' => 72],
        'Truculenta' => ['def' => 14, 'min' => 12, 'max' => 72],
    ];

}




/**
 *
 * Method to get Google webfonts with variables.
 *
 */
function theme_mb2nl_variable_gfonts() {

    return ['42dot Sans', 'AR One Sans', 'Advent Pro', 'Afacad', 'Afacad Flux', 'Agu Display', 'Akshar', 'Albert Sans', 'Alegreya',
    'Aleo', 'Alexandria', 'Alkatra', 'Alumni Sans', 'Anaheim', 'Andada Pro', 'Anek Bangla', 'Anek Devanagari', 'Anek Gujarati',
    'Anek Gurmukhi', 'Anek Kannada', 'Anek Latin', 'Anek Malayalam', 'Anek Odia', 'Anek Tamil', 'Anek Telugu', 'Antonio', 'Anuphan',
    'Anybody', 'Archivo', 'Archivo Narrow', 'Arima', 'Arimo', 'Asap', 'Assistant', 'Atkinson Hyperlegible Mono',
    'Atkinson Hyperlegible Next', 'Azeret Mono', 'Ballet', 'Baloo 2', 'Baloo Bhai 2', 'Baloo Bhaijaan 2', 'Baloo Bhaina 2',
    'Baloo Chettan 2', 'Baloo Da 2', 'Baloo Paaji 2', 'Baloo Tamma 2', 'Baloo Tammudu 2', 'Baloo Thambi 2', 'Beiruti', 'Besley',
    'Big Shoulders', 'Big Shoulders Inline', 'Big Shoulders Stencil', 'BioRhyme', 'Bitter', 'Bodoni Moda', 'Bodoni Moda SC',
    'Bricolage Grotesque', 'Brygada 1918', 'Buenard', 'Cabin', 'Cairo', 'Cairo Play', 'Catamaran', 'Caveat', 'Changa', 'Chivo',
    'Chivo Mono', 'Cinzel', 'Climate Crisis', 'Comfortaa', 'Comme', 'Commissioner', 'Cormorant', 'Cormorant Garamond',
    'Cormorant Infant', 'Crimson Pro', 'Cuprum', 'DM Sans', 'Dancing Script', 'Danfo', 'Darker Grotesque', 'Domine', 'Dosis',
    'Doto', 'DynaPuff', 'EB Garamond', 'Eczar', 'Edu AU VIC WA NT Arrows', 'Edu AU VIC WA NT Dots', 'Edu AU VIC WA NT Guides',
    'Edu AU VIC WA NT Hand', 'Edu AU VIC WA NT Pre', 'Edu NSW ACT Foundation', 'Edu QLD Beginner', 'Edu SA Beginner',
    'Edu TAS Beginner', 'Edu VIC WA NT Beginner', 'El Messiri', 'Encode Sans', 'Encode Sans SC', 'Epilogue', 'Exo', 'Exo 2',
    'Expletus Sans', 'Familjen Grotesk', 'Faustina', 'Figtree', 'Finlandica', 'Fira Code', 'Foldit', 'Frank Ruhl Libre', 'Fraunces',
    'Fredoka', 'Funnel Display', 'Funnel Sans', 'Fustat', 'Gabarito', 'Gantari', 'Geist', 'Geist Mono', 'Gelasio', 'Gemunu Libre',
    'Genos', 'Geologica', 'Georama', 'Glory', 'Gluten', 'Golos Text', 'Grandstander', 'Grenze Gotisch', 'Hahmlet', 'Handjet',
    'Hanken Grotesk', 'Hedvig Letters Serif', 'Heebo', 'Hepta Slab', 'Honk', 'Host Grotesk', 'Hubot Sans', 'IBM Plex Sans',
    'Ibarra Real Nova', 'Imbue', 'Inclusive Sans', 'Inconsolata', 'Instrument Sans', 'Inter', 'Inter Tight', 'Jaro',
    'JetBrains Mono', 'Josefin Sans', 'Josefin Slab', 'Jost', 'Jura', 'Kablammo', 'Kalnia', 'Kalnia Glaze', 'Kameron',
    'Kantumruy Pro', 'Karla', 'Kode Mono', 'Kreon', 'Kufam', 'Kumbh Sans', 'Labrada', 'League Gothic', 'League Spartan',
    'Lemonada', 'Lexend', 'Lexend Deca', 'Lexend Exa', 'Lexend Giga', 'Lexend Mega', 'Lexend Peta', 'Lexend Tera', 'Lexend Zetta',
    'Libre Bodoni', 'Libre Franklin', 'Linefont', 'Literata', 'Lora', 'M PLUS 1 Code', 'M PLUS Code Latin', 'Mada', 'Manrope',
    'Manuale', 'Marhey', 'Markazi Text', 'Martian Mono', 'Maven Pro', 'Merienda', 'Merriweather', 'Merriweather Sans',
    'Miriam Libre', 'Moderustic', 'Mohave', 'Mona Sans', 'Monda', 'Montagu Slab', 'Montserrat', 'Montserrat Underline', 'Mulish',
    'Murecho', 'MuseoModerno', 'Nabla', 'National Park', 'Newsreader', 'Noto Emoji', 'Noto Kufi Arabic', 'Noto Naskh Arabic',
    'Noto Nastaliq Urdu', 'Noto Rashi Hebrew', 'Noto Sans', 'Noto Sans Adlam', 'Noto Sans Adlam Unjoined', 'Noto Sans Arabic',
    'Noto Sans Armenian', 'Noto Sans Balinese', 'Noto Sans Bamum', 'Noto Sans Bassa Vah', 'Noto Sans Bengali',
    'Noto Sans Canadian Aboriginal', 'Noto Sans Cham', 'Noto Sans Cherokee', 'Noto Sans Devanagari', 'Noto Sans Display',
    'Noto Sans Ethiopic', 'Noto Sans Georgian', 'Noto Sans Gujarati', 'Noto Sans Gunjala Gondi', 'Noto Sans Gurmukhi',
    'Noto Sans HK', 'Noto Sans Hanifi Rohingya', 'Noto Sans Hebrew', 'Noto Sans JP', 'Noto Sans Javanese', 'Noto Sans KR',
    'Noto Sans Kannada', 'Noto Sans Kawi', 'Noto Sans Kayah Li', 'Noto Sans Khmer', 'Noto Sans Lao', 'Noto Sans Lao Looped',
    'Noto Sans Lisu', 'Noto Sans Malayalam', 'Noto Sans Medefaidrin', 'Noto Sans Meetei Mayek', 'Noto Sans Mono',
    'Noto Sans NKo Unjoined', 'Noto Sans Nag Mundari', 'Noto Sans New Tai Lue', 'Noto Sans Ol Chiki', 'Noto Sans Oriya',
    'Noto Sans SC', 'Noto Sans Sinhala', 'Noto Sans Sora Sompeng', 'Noto Sans Sundanese', 'Noto Sans Symbols', 'Noto Sans Syriac',
    'Noto Sans Syriac Eastern', 'Noto Sans TC', 'Noto Sans Tai Tham', 'Noto Sans Tamil', 'Noto Sans Tangsa', 'Noto Sans Telugu',
    'Noto Sans Thaana', 'Noto Sans Thai', 'Noto Sans Vithkuqi', 'Noto Serif', 'Noto Serif Armenian', 'Noto Serif Bengali',
    'Noto Serif Devanagari', 'Noto Serif Display', 'Noto Serif Ethiopic', 'Noto Serif Georgian', 'Noto Serif Gujarati',
    'Noto Serif Gurmukhi', 'Noto Serif HK', 'Noto Serif Hebrew', 'Noto Serif Hentaigana', 'Noto Serif JP', 'Noto Serif KR',
    'Noto Serif Kannada', 'Noto Serif Khmer', 'Noto Serif Khojki', 'Noto Serif Lao', 'Noto Serif Malayalam', 'Noto Serif NP Hmong',
    'Noto Serif Oriya', 'Noto Serif SC', 'Noto Serif Sinhala', 'Noto Serif TC', 'Noto Serif Tamil', 'Noto Serif Telugu',
    'Noto Serif Thai', 'Noto Serif Tibetan', 'Noto Serif Toto', 'Noto Serif Vithkuqi', 'Noto Serif Yezidi',
    'Noto Traditional Nushu', 'Nunito', 'Nunito Sans', 'Ojuju', 'Onest', 'Open Sans', 'Orbitron', 'Oswald', 'Outfit', 'Overpass',
    'Overpass Mono', 'Oxanium', 'Parkinsans', 'Pathway Extreme', 'Petrona', 'Phudu', 'Piazzolla', 'Pixelify Sans', 'Platypi',
    'Playfair', 'Playfair Display', 'Playpen Sans', 'Playwrite AR', 'Playwrite AT', 'Playwrite AU NSW', 'Playwrite AU QLD',
    'Playwrite AU SA', 'Playwrite AU TAS', 'Playwrite AU VIC', 'Playwrite BE VLG', 'Playwrite BE WAL', 'Playwrite BR',
    'Playwrite CA', 'Playwrite CL', 'Playwrite CO', 'Playwrite CU', 'Playwrite CZ', 'Playwrite DE Grund', 'Playwrite DE LA',
    'Playwrite DE SAS', 'Playwrite DE VA', 'Playwrite DK Loopet', 'Playwrite DK Uloopet', 'Playwrite ES', 'Playwrite ES Deco',
    'Playwrite FR Moderne', 'Playwrite FR Trad', 'Playwrite GB J', 'Playwrite GB S', 'Playwrite HR', 'Playwrite HR Lijeva',
    'Playwrite HU', 'Playwrite ID', 'Playwrite IE', 'Playwrite IN', 'Playwrite IS', 'Playwrite IT Moderna', 'Playwrite IT Trad',
    'Playwrite MX', 'Playwrite NG Modern', 'Playwrite NL', 'Playwrite NO', 'Playwrite NZ', 'Playwrite PE', 'Playwrite PL',
    'Playwrite PT', 'Playwrite RO', 'Playwrite SK', 'Playwrite TZ', 'Playwrite US Modern', 'Playwrite US Trad', 'Playwrite VN',
    'Playwrite ZA', 'Plus Jakarta Sans', 'Podkova', 'Poltawski Nowy', 'Pontano Sans', 'Public Sans', 'Quicksand', 'Radio Canada',
    'Radio Canada Big', 'Raleway', 'Rasa', 'Readex Pro', 'Recursive', 'Red Hat Display', 'Red Hat Mono', 'Red Hat Text', 'Red Rose',
    'Reddit Mono', 'Reddit Sans', 'Reddit Sans Condensed', 'Reem Kufi', 'Reem Kufi Fun', 'Rethink Sans', 'Roboto',
    'Roboto Condensed', 'Roboto Flex', 'Roboto Mono', 'Roboto Serif', 'Roboto Slab', 'Rokkitt', 'Rosario', 'Rubik', 'Ruda',
    'STIX Two Text', 'Saira', 'Sansita Swashed', 'Schibsted Grotesk', 'Sen', 'Shantell Sans', 'Signika', 'Signika Negative',
    'Sixtyfour', 'Sixtyfour Convergence', 'Smooch Sans', 'Sofia Sans', 'Sofia Sans Condensed', 'Sofia Sans Extra Condensed',
    'Sofia Sans Semi Condensed', 'Sometype Mono', 'Sono', 'Sora', 'Sour Gummy', 'Source Code Pro', 'Source Sans 3',
    'Source Serif 4', 'Space Grotesk', 'Spline Sans', 'Spline Sans Mono', 'Stick No Bills', 'Syne', 'Teachers', 'Teko',
    'Tektur', 'Texturina', 'Tilt Neon', 'Tilt Prism', 'Tilt Warp', 'Tourney', 'Trispace', 'Truculenta', 'Ubuntu Sans',
    'Ubuntu Sans Mono', 'Unbounded', 'Urbanist', 'Varta', 'Vazirmatn', 'Victor Mono', 'Vollkorn', 'Wavefont', 'Winky Rough',
    'Winky Sans', 'Wittgenstein', 'Wix Madefor Display', 'Wix Madefor Text', 'Work Sans', 'Workbench', 'Yaldevi',
    'Yanone Kaffeesatz', 'Yrsa', 'Ysabeau', 'Ysabeau Infant', 'Ysabeau Office', 'Ysabeau SC'];

}





/**
 *
 * Method to get Google webfonts
 *
 */
function theme_mb2nl_gfonts() {
    global $PAGE;

    $output = '';
    $i = 0;
    $fonts = theme_mb2nl_gfonts_arr();

    if (!theme_mb2nl_is_gfonts() || !count($fonts)) {
        return;
    }

    $output .= '<link rel="preconnect" href="//fonts.googleapis.com">';
    $output .= '<link rel="preconnect" href="//fonts.gstatic.com" crossorigin>';
    $output .= '<link href="//fonts.googleapis.com/css2';

    foreach ($fonts as $k => $f) {

        if (!$f['name'] || !$f['set']) {
            continue;
        }

        $i++;

        $pref = $i == 1 ? '?' : '&';
        $output .= $pref . 'family=' . $f['name'];
        $output .= ':' . $f['style'];

    }

    $output .= '&display=swap" rel="stylesheet">';

    return $output;

}






/**
 *
 * Method to get custom fonts
 *
 */
function theme_mb2nl_custom_fonts() {

    global $PAGE;
    $output = '';

    for ($i = 1; $i <= 3; $i++) {
        $fonts = theme_mb2nl_filearea('cfontfiles' . $i, false);
        $fontname = theme_mb2nl_theme_setting($PAGE, 'cfont' . $i);
        $x = 0;

        if (count($fonts) && $fontname) {
            $output .= '@font-face {';
            $output .= 'font-family:\'' .$fontname . '\';';
            $output .= 'src: ';

            foreach ($fonts as $f) {
                $x++;
                $finfo = pathinfo($f);
                $sep = $x == count($fonts) ? ';' : ', ';
                $format = $finfo['extension'];

                if ($finfo['extension'] === 'ttf') {
                    $format = 'truetype';
                }

                $output .= 'url(\'' . $finfo['dirname'] . '/' . $finfo['basename'] . '\') format(\'' . $format . '\')' . $sep;
            }

            $output .= '}';
        }
    }

    return $output;

}




/**
 *
 * Method to get font icons
 *
 */
function theme_mb2nl_fonticons() {
    global $PAGE;
    $output = '';

    $output .= theme_mb2nl_fontface('Lineicons');
    $output .= theme_mb2nl_fontface('remixicon', true);
    $output .= theme_mb2nl_fontface('bootstrap-icons', true);

    if (theme_mb2nl_theme_setting($PAGE, 'acsboptions') && theme_mb2nl_theme_setting($PAGE, 'dyslexic')) {
        $output .= theme_mb2nl_fontface('opendyslexic', true);
    }

    return $output;

}





/**
 *
 * Method to get font icons
 *
 */
function theme_mb2nl_fontface($fontname, $woff2 = false) {
    global $CFG;

    $output = '';
    $assetsur = $CFG->wwwroot . theme_mb2nl_themedir() . '/mb2nl/assets/' . $fontname . '/fonts/';
    $fonfamily = $fontname;
    $svgfontname = $fontname;
    $enbl = true;
    $comma = ', ';
    $comma2 = ', ';

    if ($fontname === 'opendyslexic') {
        $enbl = false;
        $fonfamily = 'OpenDyslexic';
        $comma = ';';
    }

    $woff = $enbl;

    if ($fontname === 'bootstrap-icons' || $fontname === 'Lineicons') {
        $enbl = false;
        $woff = true;
        $comma2 = ';';
    }

    $output .= '@font-face {';
    $output .= 'font-family:\'' . $fonfamily . '\';';
    $output .= 'src: ';
    $output .= $woff2 ? 'url(\'' . $assetsur . $fontname . '.woff2\') format(\'woff2\')' . $comma : '';// Super Modern Browsers.
    $output .= $woff ? 'url(\'' . $assetsur . $fontname . '.woff\') format(\'woff\')' . $comma2 : ''; // Pretty Modern Browsers.
    $output .= $enbl ? 'url(\'' . $assetsur . $fontname . '.ttf\') format(\'truetype\'),' : ''; // Safari, Android, iOS.
    $output .= $enbl ? 'url(\'' . $assetsur . $fontname . '.svg#' . $svgfontname . '\') format(\'svg\');' : ''; // Legacy iOS.
    $output .= 'font-weight: normal;';
    $output .= 'font-style: normal;';
    $output .= '}';

    return $output;

}




/**
 *
 * Method to get font family setting
 *
 */
function theme_mb2nl_get_fonf_family($page, $font) {

    return '\'' . theme_mb2nl_theme_setting($page, $font) . '\'';

}






/**
 *
 * Method to get font icons for plugins (page builder and megamenu)
 *
 */
function theme_mb2nl_get_icons4plugins() {

    return [
        'font-awesome' => [
            'name' => 'Font Awesome',
            'folder' => 'font-awesome',
            'css' => 'font-awesome',
            'prefhtml' => 'fa ',
            'tabid' => 'tab-font-icons-fa',
        ],
        'remixicon' => [
            'name' => 'Remix icons',
            'folder' => 'remixicon',
            'css' => 'remixicon',
            'prefhtml' => '',
            'tabid' => 'tab-font-icons-remix',
        ],
        'bootstrap-icons' => [
            'name' => 'Bootstrap icons',
            'folder' => 'bootstrap-icons',
            'css' => 'bootstrap-icons',
            'prefhtml' => 'bi ',
            'tabid' => 'tab-font-bootstrap-icons',
        ],
        'Lineicons' => [
            'name' => 'Line icons',
            'folder' => 'Lineicons',
            'css' => 'Lineicons',
            'prefhtml' => '',
            'tabid' => 'tab-font-icons-lineicons',
        ],
    ];

}
