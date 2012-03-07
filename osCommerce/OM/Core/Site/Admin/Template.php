<?php
/**
 * osCommerce Online Merchant
 * 
 * @copyright Copyright (c) 2012 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  namespace osCommerce\OM\Core\Site\Admin;

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  class Template extends \osCommerce\OM\Core\Template {
    protected $_templates = array();

    protected $_default_template = 'Sail';

    public function __construct() {
      $this->_templates = array('id' => 0,
                                'code' => 'Sail',
                                'title' => 'Sail');

      $this->set($this->_default_template);
    }

    public static function getTemplates() {
      return $this->_templates;
    }

    public function getTemplateFile($file = null, $template = null) {
      $reset_base_file = false;

      if ( !isset($template) ) {
        $template = $this->_template;
      }

      if ( !isset($file) ) {
        $reset_base_file = true;

        $file = call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'getBaseFilename'));
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Content/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Content/' . $file;
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Content/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Content/' . $file;
      }

      if ( call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'hasParent')) ) {
        $template = call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'getParent'));

        if ( $reset_base_file === true ) {
          $file = null;
        }

        return $this->getTemplateFile($file, $template);
      }

      trigger_error('Template::getTemplateFile() $file does not exist: ' . $file);

      return false;
    }

    public function getPageContentsFile() {
      $file = $this->getPageContentsFilename();

      $template = $this->_template;

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file;
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file;
      }

      if ( call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'hasParent')) ) {
        while ( true ) {
          $template = call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'getParent'));

          if ( file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file) ) {
            return OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file;
          }

          if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file) ) {
            return OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Template/' . $template . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file;
          }

          if ( !call_user_func(array('osCommerce\\OM\\Core\\Site\\' . OSCOM::getSite() . '\\Template\\' . $template . '\\Controller', 'hasParent')) ) {
            break;
          }
        }
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Custom/Site/' . OSCOM::getSite() . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file;
      }

      if ( file_exists(OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file) ) {
        return OSCOM::BASE_DIRECTORY . 'Core/Site/' . OSCOM::getSite() . '/Application/' . OSCOM::getSiteApplication() . '/pages/' . $file;
      }

      trigger_error('Template::getPageContentsFile() $file does not exist: ' . $file);

      return false;
    }

    public function set($code = null) {
      $template = $this->_default_template;

      if ( isset($code) ) {
        $template = $code;
      } elseif ( isset($_GET['template']) && !empty($_GET['template']) && $this->exists($_GET['template']) ) {
        $template = $_GET['template'];
      } elseif ( isset($_SESSION[OSCOM::getSite()]['template']) ) {
        $template = $_SESSION[OSCOM::getSite()]['template'];
      }

      if ( $template != $this->_default_template ) {
        if ( !isset($_SESSION[OSCOM::getSite()]['template']) || ($_SESSION[OSCOM::getSite()]['template'] != $template) ) {
          $_SESSION[OSCOM::getSite()]['template'] = $template;
        }
      }

      $this->_template = $template;
    }

    public function exists($code) {
      foreach ( $this->_templates as $t ) {
        if ( $t['code'] == $code ) {
          return true;
        }
      }

      return false;
    }

    public function getIcon($size = 16, $icon = null, $title = null) {
      if ( !isset($icon) ) {
        $icon = $this->_application->getIcon();
      }

      return HTML::image(OSCOM::getPublicSiteLink('images/applications/' . $size . '/' . $icon), $title, $size, $size);
    }
  }
?>
