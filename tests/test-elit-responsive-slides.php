<?php

/**
 * 
 */
class Test_Elit_Responsive_Slides extends WP_UnitTestCase {
  
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

  function testGetAttsDefaultsPager() {
    $attrs = array();
    $expected = false;
    $atts = $this->plugin->get_atts( $attrs );
    $pager = $atts['pager'];
    $this->assertSame( $expected, $pager );
  }

  function testGetAttsIDs() {
    $attrs = array( 'ids' => '123, 456, 789' );
    $expected = '123, 456, 789';

    $atts = $this->plugin->get_atts( $attrs );
    $ids = $atts['ids'];
    $this->assertEquals( $expected, $ids, 'testGetsAttsIDs expects an array of ids' );
  }

  function testGetAttsAutoBool() {
    $attrs = array( 'auto' => false );
    $expected = $attrs['auto'];

    $a = $this->plugin->get_atts( $attrs );
    $this->assertEquals( $expected, $a['auto'] );
  }

  function testGetAttsAutoStr() {
    $attrs = array( 'auto' => false );
    $expected = false;

    $a = $this->plugin->get_atts( $attrs );
    $this->assertEquals( $expected, $a['auto'] );
  }

  function testGetAttsPager() {
    $attrs = array( 'pager' => false );
    $expected = false;

    $a = $this->plugin->get_atts( $attrs );
    $this->assertEquals( $expected, $a['pager'] );
  }

} // eoc
