<?php
/**
 * test case
 */

require_once 'BaseTest.php';

/**
 * test case.
 */
class WURFL_Request_UserAgentNormalizer_BlackBerryTest extends WURFL_Request_UserAgentNormalizer_BaseTest
{
    public function setUp()
    {
        $this->normalizer = new WURFL_Request_UserAgentNormalizer_Generic_BlackBerry();
    }

    /**
     * @test
     * @dataProvider blackberryUserAgentsDataProvider
     *
     */
    public function shouldRemoveAllCharactersBeforeTheLastBlackberryString($userAgent, $expected)
    {
        $found = $this->normalizer->normalize($userAgent);
        $this->assertEquals($expected, $found);
    }

    public function blackberryUserAgentsDataProvider()
    {
        return [
            ["Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; en) AppleWebKit/534.1+ (KHTML, like Gecko) Version/6.0.0.135 Mobile Safari/534.1+", "Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; en) AppleWebKit/534.1+ (KHTML, like Gecko) Version/6.0.0.135 Mobile Safari/534.1+" ],
            ["Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0) BlackBerry8800/4.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/134", "BlackBerry8800/4.2.1 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/134" ],
            ["BlackBerry", "BlackBerry" ],
        ];
    }
}
