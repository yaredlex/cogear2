<?php

/**
 * Jevix filter
 *
 * @author		Беляев Дмитрий <admin@cogear.ru>
 * @copyright		Copyright (c) 2012, Беляев Дмитрий
 * @license		http://cogear.ru/license.html
 * @link		http://cogear.ru
 */
class Parser_Filter extends Form_Filter_Abstract {

    /**
     * Filter
     *
     * @value
     */
    public function filter($value) {
        if(access('Parser.off') && cogear()->input->post('parser_off')){
            return $value;
        }
        $jevix = new Parser_Jevix();

//Конфигурация
        $allowed_tags = array('a', 'img', 'i', 'b', 'u', 'em', 'strong', 'nobr', 'li', 'ol', 'ul', 'sup', 'abbr', 'pre', 'acronym', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'cut', 'user', 'br', 'code', 'p','video','table', 'tbody', 'tr', 'td');
// 1. Устанавливаем разрешённые теги. (Все не разрешенные теги считаются запрещенными.)
        $jevix->cfgAllowTags($allowed_tags);

// 2. Устанавливаем коротие теги. (не имеющие закрывающего тега)
        $jevix->cfgSetTagShort(array('br', 'img'));

// 3. Устанавливаем преформатированные теги. (в них все будет заменятся на HTML сущности)
        $jevix->cfgSetTagPreformatted(array('code'));

// 4. Устанавливаем теги, которые необходимо вырезать из текста вместе с контентом.
        $jevix->cfgSetTagCutWithContent(array('script', 'object', 'iframe', 'style'));

// 5. Устанавливаем разрешённые параметры тегов. Также можно устанавливать допустимые значения этих параметров.
        $jevix->cfgAllowTagParams('a', array('title', 'href','class'));
        $jevix->cfgAllowTagParams('img', array('src', 'alt' => '#text', 'title', 'align' => array('right', 'left', 'center'), 'width' => '#int', 'height' => '#int', 'hspace' => '#int', 'vspace' => '#int','class'));
        $jevix->cfgAllowTagParams('code', array('class'));
        $jevix->cfgAllowTagParams('p', array('align' => array('left', 'right', 'center')));
        $jevix->cfgAllowTagParams('pre', array('class'));
        $jevix->cfgAllowTagParams('b', array('class'));

// 6. Устанавливаем параметры тегов являющиеся обязяательными. Без них вырезает тег оставляя содержимое.
        $jevix->cfgSetTagParamsRequired('img', 'src');
        $jevix->cfgSetTagParamsRequired('a', 'href');

// 7. Устанавливаем теги которые может содержать тег контейнер
//    cfgSetTagChilds($tag, $childs, $isContainerOnly, $isChildOnly)
//       $isContainerOnly : тег является только контейнером для других тегов и не может содержать текст (по умолчанию false)
//       $isChildOnly : вложенные теги не могут присутствовать нигде кроме указанного тега (по умолчанию false)
//$jevix->cfgSetTagChilds('ul', 'li', true, false);
// 8. Устанавливаем атрибуты тегов, которые будут добавлятся автоматически
        $jevix->cfgSetTagParamDefault('a', 'rel', null, true);
//$jevix->cfgSetTagParamsAutoAdd('a', array('rel' => 'nofollow'));
//$jevix->cfgSetTagParamsAutoAdd('a', array('name'=>'rel', 'value' => 'nofollow', 'rewrite' => true));
//        $jevix->cfgSetTagParamDefault('img', 'width', '300px');
//        $jevix->cfgSetTagParamDefault('img', 'height', '300px');
//$jevix->cfgSetTagParamsAutoAdd('img', array('width' => '300', 'height' => '300'));
//$jevix->cfgSetTagParamsAutoAdd('img', array(array('name'=>'width', 'value' => '300'), array('name'=>'height', 'value' => '300') ));
// 9. Устанавливаем автозамену
        $jevix->cfgSetAutoReplace(array(' -- ', '+/-', '(c)', '(r)'), array(' &mdash; ', '±', '©', '®'));

// 10. Включаем или выключаем режим XHTML. (по умолчанию включен)
        $jevix->cfgSetXHTMLMode(TRUE);

// 11. Включаем или выключаем режим замены переноса строк на тег <br/>. (по умолчанию включен)
        $jevix->cfgSetAutoBrMode(TRUE);

// 12. Включаем или выключаем режим автоматического определения ссылок. (по умолчанию включен)
        $jevix->cfgSetAutoLinkMode(FALSE);
// 13. Отключаем типографирование в определенном теге
        $jevix->cfgSetTagNoTypography(array('code','pre'));
//        $jevix->cfgSetTagNoTypography('pre');
        event('jevix', $jevix);
        $errors = array();
        $result = $jevix->parse($value, $errors);
        return $result;
    }

}
