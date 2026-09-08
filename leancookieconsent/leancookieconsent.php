<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class LeanCookieConsent extends Module
{
    private const CONFIG_SITE_KEY = 'LEAN_COOKIE_CONSENT_SITE_KEY';
    private const RUNTIME_BASE_URL = 'https://api.leancookieconsent.com';

    public function __construct()
    {
        $this->name = 'leancookieconsent';
        $this->tab = 'front_office_features';
        $this->version = '0.1.0';
        $this->author = 'Black Lotus Consulting Srl';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = [
            'min' => '1.7.6.0',
            'max' => _PS_VERSION_,
        ];

        parent::__construct();

        $this->displayName = $this->l('Lean Cookie Consent');
        $this->description = $this->l('Load Lean Cookie Consent on your storefront from a public Site Key.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall Lean Cookie Consent?');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && Configuration::updateValue(self::CONFIG_SITE_KEY, '');
    }

    public function uninstall()
    {
        return Configuration::deleteByName(self::CONFIG_SITE_KEY)
            && parent::uninstall();
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitLeanCookieConsent')) {
            $siteKey = trim((string) Tools::getValue(self::CONFIG_SITE_KEY));

            if ($siteKey !== '' && !preg_match('/^[A-Za-z0-9_-]+$/', $siteKey)) {
                $output .= $this->displayError($this->l('The Site Key can contain only letters, numbers, hyphens and underscores.'));
            } else {
                Configuration::updateValue(self::CONFIG_SITE_KEY, $siteKey);
                $output .= $this->displayConfirmation($this->l('Settings updated.'));
            }
        }

        return $output . $this->renderForm();
    }

    public function hookDisplayHeader()
    {
        $siteKey = trim((string) Configuration::get(self::CONFIG_SITE_KEY));

        if ($siteKey === '') {
            return '';
        }

        $shopName = $this->context->shop ? $this->context->shop->name : '';
        $shopDomain = $this->context->shop ? $this->context->shop->domain_ssl : '';

        $this->context->smarty->assign([
            'lean_site_key_json' => json_encode($siteKey, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
            'lean_shop_name_json' => json_encode($shopName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
            'lean_shop_domain_json' => json_encode($shopDomain, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT),
            'lean_runtime_url' => self::RUNTIME_BASE_URL . '/embed.js?site=' . rawurlencode($siteKey),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/header.tpl');
    }

    private function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int) Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLeanCookieConsent';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => [
                self::CONFIG_SITE_KEY => Tools::getValue(self::CONFIG_SITE_KEY, Configuration::get(self::CONFIG_SITE_KEY)),
            ],
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    private function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Lean Cookie Consent settings'),
                    'icon' => 'icon-shield',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Site Key'),
                        'name' => self::CONFIG_SITE_KEY,
                        'required' => false,
                        'desc' => $this->l('Paste the public Site Key from your Lean Cookie Consent dashboard.'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }
}

