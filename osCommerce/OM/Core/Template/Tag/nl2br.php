<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Template\Tag;

  use osCommerce\OM\Core\HTML;

  class nl2br extends \osCommerce\OM\Core\Template\TagAbstract {
    static public function execute($string) {
      return nl2br($string);
    }
  }
?>
