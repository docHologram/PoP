<?php

class PoP_ServiceWorkers_Frontend_ResourceLoader_ScriptsRegistration {

	function register_scripts() {

		global $pop_resourceloaderprocessor_manager, $popfrontend_resourceloader_scriptsregistration;

		// Get all the resources
		$resources = $pop_resourceloaderprocessor_manager->get_resources();

		// Add a hook to remove unwanted resources. Eg:
		// POP_RESOURCELOADER_RESOURCELOADERCONFIG_EXTERNAL and POP_RESOURCELOADER_RESOURCELOADERCONFIG_EXTERNALRESOURCES
		// (These only make sense to be added on the External page)
		$resources = apply_filters(
			'PoP_ServiceWorkers_Frontend_ResourceLoader_ScriptsRegistration:register_scripts',
			$resources
		);

		// Comment Leo 13/11/2017: no need for bundle/bundlegroups to be registered here, since we're just enqueuing resources for the AppShell
		// // Calculate the bundles and bundlegroups
		// $generatedfiles = $popfrontend_resourceloader_scriptsregistration->calculate_bundles($resources);
		// $bundles = $generatedfiles['bundles'];
		// $bundlegroups = $generatedfiles['bundle-groups'];
		$bundles = array();
		$bundlegroups = array();

		// Comment Leo 14/11/2017: this is not needed anymore, since making the AppShell always use the 'resource' type, independently of the value set by configuration
		// // However, we must register the bundle(group)s needed by the appshell, or it won't be able to load offline the first time
		// $enqueuefile_type = PoP_Frontend_ServerUtils::get_enqueuefile_type();
		// if ($enqueuefile_type == 'bundlegroup' || $enqueuefile_type == 'bundle') {

		// 	$appshell_pathkeyresources = array();
		// 	$template_id = GD_TEMPLATE_TOPLEVEL_PAGE;
	 //        $hierarchy = GD_SETTINGS_HIERARCHY_PAGE;
	 //        $ids = array(
	 //            POP_SERVICEWORKERS_PAGE_APPSHELL,
	 //        ); 
	 //        $merge = false;
	 //        $fetching_json = false;
	 //        PoP_ResourceLoaderProcessorUtils::add_resources_from_current_vars($fetching_json, $appshell_pathkeyresources, $template_id, $ids, $merge);

	 //        // Must recover the resources from under the nested levels: $resources[$path][$key] = $actual_resources
	 //        $enqueue_appshell_resources = array();
	 //        foreach ($appshell_pathkeyresources as $appshell_path => $appshell_keyresources) {
		        
		//         foreach ($appshell_keyresources as $appshell_key => $appshell_resources) {

		//         	$enqueue_appshell_resources = array_merge(
		//         		$enqueue_appshell_resources,
		// 	        	$appshell_resources
		// 	        );
		// 		}
	 //        }

	 //        $generatedfiles = $popfrontend_resourceloader_scriptsregistration->calculate_bundles($enqueue_appshell_resources);
		// 	$bundles = $generatedfiles['bundles'];
		// 	$bundlegroups = $generatedfiles['bundle-groups'];
		// }

		// Comment Leo 14/11/2017: no need for $remove_bundled_resources, since the AppShell only deals with 'resource', so $bundle and $bundlegroups will be empty
		// // Register them, but do not remove the individual resources, so they are added to the SW precache list
		// $remove_bundled_resources = false;
		$popfrontend_resourceloader_scriptsregistration->register_resources($resources, $bundles, $bundlegroups/*, $remove_bundled_resources*/);
	}
}

/**---------------------------------------------------------------------------------------------------------------
 * Initialization
 * ---------------------------------------------------------------------------------------------------------------*/
global $pop_serviceworkers_frontend_resourceloader_scriptsregistration;
$pop_serviceworkers_frontend_resourceloader_scriptsregistration = new PoP_ServiceWorkers_Frontend_ResourceLoader_ScriptsRegistration();
