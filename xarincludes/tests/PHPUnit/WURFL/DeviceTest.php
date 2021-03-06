<?php
/**
 * test case
 */
require_once dirname(__FILE__).'/classautoloader.php';
require_once 'PHPUnit/Framework.php';

class WURFL_DeviceTest extends PHPUnit_Framework_TestCase
{
    public const RESOURCES_DIR = "../../resources";
    public const CACHE_DIR = "../../resources/cache";

    private static $persistenceStorage;
    private static $wurflManagerFactory;
    private static $wurflManager;

    protected $testData;

    public const TEST_DATA_FILE = "../../resources/device-capability.yml";

    public static function setUpBeforeClass()
    {
        self::createWurflManger();
    }

    public static function tearDownAfterClass()
    {
    }

    public static function createWurflManger()
    {
        $resourcesDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::RESOURCES_DIR;
        $cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::CACHE_DIR;
        $config = new WURFL_Configuration_InMemoryConfig();

        $config->wurflFile($resourcesDir . "/wurfl-regression.xml")
                ->wurflPatch($resourcesDir . "/web_browsers_patch.xml")
                ->wurflPatch($resourcesDir . "/spv_patch.xml")
                ->wurflPatch($resourcesDir . "/android_patch.xml")
                ->wurflPatch($resourcesDir . "/new_devices.xml");

        $params = [
            "dir" => $cacheDir,
            WURFL_Configuration_Config::EXPIRATION => 0, ];
        $config->persistence("file", $params);
        self::$persistenceStorage = new WURFL_Storage_Memory($params);
        self::$wurflManagerFactory = new WURFL_WURFLManagerFactory($config, self::$persistenceStorage);
        self::$wurflManager = self::$wurflManagerFactory->create();
    }

    /**
     * @dataProvider deviceIdCapabilityNameCapabilityValueProvider
     */
    public function testGetCapability($deviceId, $capabilityName, $capabilityValue)
    {
        $this->checkDeps();
        $device = self::$wurflManager->getDevice($deviceId);
        $capabilityFound = $device->getCapability($capabilityName);

        $this->assertEquals($capabilityValue, $capabilityFound);
    }

    public function deviceIdCapabilityNameCapabilityValueProvider()
    {
        return [
            ["ericsson_t20_ver1", "resolution_width", "101" ],
            ["ericsson_t20_ver1", "resolution_width", "101" ],
            ["ericsson_t20_ver1", "resolution_height", "33" ],
            ["ericsson_t20_ver1", "brand_name", "Ericsson" ],
            ["ericsson_t20_ver1", "icons_on_menu_items_support", "false" ],
            ["verizon_lge_vx8100_ver1", "ringtone_midi_monophonic", "false" ],
            ["verizon_lge_vx8100_ver1", "gif_animated", "true" ],
            ["verizon_lge_vx8100_ver1", "xhtml_format_as_css_property", "true" ],
            ["verizon_lge_vx8100_ver1", "oma_v_1_0_forwardlock", "true" ],
            ["kwc_kx9_ver1", "brand_name", "Kyocera" ],
            ["kwc_kx9_ver1", "xhtml_marquee_as_css_property", "true" ],
            ["kwc_kx9_ver1", "oma_v_1_0_forwardlock", "true" ],
            ["kwc_kx9_ver1", "html_wi_imode_html_1", "true" ],
            ["kwc_kx9_ver1", "menu_with_list_of_links_recommended", "false" ],
            ["kwc_kx9_ver1", "insert_br_element_after_widget_recommended", "false" ],
            ["blackberry7780_ver1", "model_name", "BlackBerry 7780" ],
            ["blackberry7780_ver1", "midi_monophonic", "true" ],
            ["blackberry7780_ver1", "html_wi_w3_xhtmlbasic", "true" ],
            ["nokia_6610_ver1", "mp3", "false" ],
            ["nokia_6610_ver1", "max_deck_size", "65535" ],
            ["nokia_6300_ver1_sub0470", "mp3", "false" ],
            ["firefox_3", "brand_name", "firefox" ],
            ["firefox_3", "model_name", "3" ],
            ["firefox_2", "is_wireless_device", "false" ],
            ["firefox_2", "resolution_width", "800" ],
            ["firefox_2", "resolution_height", "600" ],
        ];
    }

    private function checkDeps()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped("PHP extension 'memcache' must be loaded and a local memcache server running to run this test.");
        }
    }
}
