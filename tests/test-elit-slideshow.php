<?php

/**
 * 
 */
class Test_Elit_Slideshow extends WP_UnitTestCase {
  
  private $plugin;
  
	function setUp() {
	  parent::setUp();
    $this->plugin = $GLOBALS['elit-slideshow'];
	}

  function testAssertTrue() {
		$this->assertTrue( true );
  }

  function testPluginActive() {
    $this->assertFalse( 
      null == $this->plugin, 
      'testPluginActive says our plugin is not loaded' 
    );
  }

} // eoc
