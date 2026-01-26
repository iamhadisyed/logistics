<?php
/**
 * Visual Component Interface
 *
 * The idea is to have a block of html (with it's own functionality)
 * that can be re-used on different page.
 *
 * Typical scenarios are login boxes, adverts, mini baskets, etc.
 *
 * The component monitors the value of the "form_action" element
 * to determine if an action is associated with the component.
 *
 * Components are implemented as singletons so can be used within
 * templates, classes, page code, etc independently. A factory method "getItem"
 * is provided to get the class instance.
 *
 * Typical Scenario, login box:
 * ---------------------------
 * Class could be called LoginBox (obviously implementing this interface).
 *
 * 1. The code to check if a user was logging on using this component
 * would be in the "init" method and would be called in page initialisation,
 * e.g.
 * 	LoginBox::getItem()->init();
 *
 * 2. The component would then render appropriately depending upon whether
 * login was successful.  The render code would probably be in the main page
 * template (assuming it is appearing on every page).
 * e.g.
 * 	LoginBox::getItem()->render();
 *
 * - that's it!
 *
 */
interface iVisualComponent
{
	public static function getItem();
	public function init();
	public function render();
}
