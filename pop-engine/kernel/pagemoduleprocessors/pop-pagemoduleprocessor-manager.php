<?php
namespace PoP\Engine;

class PageModuleProcessor_Manager {

	var $processors;
	// var $module_pages;
	
	function __construct() {

		PageModuleProcessorManager_Factory::set_instance($this);

		$this->processors = array();
		// $this->module_pages = array();
	}

	function add($processor) {

		foreach ($processor->get_groups() as $group) {
			
			$this->processors[$group] = $this->processors[$group] ?? array();
			$this->processors[$group][] = $processor;
		}

		// // Also keep a hashmap with the correspoding pages for each module
		// foreach ($processor->get_page_modules_by_vars_properties() as $page_id => $module_vars_properties) {
		// 	foreach ($module_vars_properties as $module => $vars_properties) {

		// 		$this->module_pages[$module] = $this->module_pages[$module] ?? array();
		// 		if (!in_array($page_id, $this->module_pages[$module])) {
		// 			$this->module_pages[$module][] = $page_id;
		// 		}
		// 	}
		// }
	}

	function get_processors($group = null) {

		$group = $group ?? POP_PAGEMODULEGROUPPLACEHOLDER_MAINCONTENTMODULE;
		return $this->processors[$group] ?? array();
	}
	
	// function get_module_pages($module) {
	
	// 	return $this->module_pages[$module] ?? array();
	// }

	function get_page_module_by_most_allmatching_vars_properties($group = null, $page_id = null, $vars = null) {

		$group = $group ?? POP_PAGEMODULEGROUPPLACEHOLDER_MAINCONTENTMODULE;
		$page_id = $page_id ?? Utils::get_hierarchy_page_id();

		// Allow to pass a custom $vars, with custom values
		$vars = $vars ?? Engine_Vars::get_vars();

		$processors = $this->get_processors($group);
		$most_matching_module = false;
		$most_matching_properties_count = -1; // Start with -1, since 0 matches is possible

		foreach ($processors as $processor) {

			$page_module_vars_properties = $processor->get_page_modules_by_vars_properties();

			// Check if this processor implements modules for this page
			if ($module_vars_properties = $page_module_vars_properties[$page_id]) {
				foreach ($module_vars_properties as $module => $vars_properties_items) {
					foreach ($vars_properties_items as $vars_properties_set) {

						// Check if the all the $vars_properties_set are satisfied <= if all those key/values are also present in $vars
						if (array_is_subset($vars_properties_set, $vars)) {

							// Check how many matches there are, and if it's the most, this is the most matching module
							// Check that it is >= instead of >. This is done so that later processors can override the behavior from previous processors,
							// which makes sense since plugins are loaded in a specific order
							if (($matching_properties_count = count($vars_properties_set, COUNT_RECURSIVE)) >= $most_matching_properties_count) {
								$most_matching_module = $module;
								$most_matching_properties_count = $matching_properties_count;
							}
						}
					}
				}
			}
		}

		// If there was a satisfying module, then return it
		// We can override the default module, for a specific page, by setting it to module null! Hence, here ask if the chosen module is not false,
		// and if so already return it, allowing for null values too (eg: POPTHEME_WASSUP_PAGE_LOADERS_INITIALFRAMES in poptheme-wassup/library/pagemoduleprocessors/pagesection-maincontent.php)
		if ($most_matching_module !== false) {
			return $most_matching_module;
		}

		// Otherwise, repeat the procedure checking for one level lower: without the page
		foreach ($processors as $processor) {

			$module_vars_properties = $processor->get_nopage_modules_by_vars_properties();
			foreach ($module_vars_properties as $module => $vars_properties_items) {
				foreach ($vars_properties_items as $vars_properties_set) {
					// Check if the all the $vars_properties are satisfied <= if all those key/values are also present in $vars
					if (array_is_subset($vars_properties_set, $vars)) {
						// Check how many matches there are, and if it's the most, this is the most matching module
						if (($matching_properties_count = count($vars_properties_set, COUNT_RECURSIVE)) >= $most_matching_properties_count) {
							$most_matching_module = $module;
							$most_matching_properties_count = $matching_properties_count;
						}
					}
				}
			}
		}

		// If it is false, then return null
		return $most_matching_module ? $most_matching_module : null;
	}
}

/**---------------------------------------------------------------------------------------------------------------
 * Initialization
 * ---------------------------------------------------------------------------------------------------------------*/
new PageModuleProcessor_Manager();
