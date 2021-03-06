<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2018 Vanilla Forums Inc.
 * @license GPLv2
 */

namespace VanillaTests\APIv2;

/**
 * Tests for the /api/v2/dashboard endpoints.
 */
class DashboardTest extends AbstractAPIv2Test {

    /**
     * A basic smoke test of the dashboard menus.
     */
    public function testIndexMenusSmoke() {
        $r = $this->api()->get('/dashboard/menus');
        $data = $r->getBody();
        $this->assertSame(3, count($data));
    }
}
