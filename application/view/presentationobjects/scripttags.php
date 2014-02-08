<?php

/*
 * @author Aleksander Lanes aleksander.lanes@gmail.com
 * @since 1.0.0
 * @version 1.0.0
 * @package Ruby
 * 
 */

namespace View\PresentationObjects;

use View\AbstractPresentationObject;

final class Scripttags extends AbstractPresentationObject
{
	public function assignData($sPage)
	{
		$aScrips = array();
		
		//The following plugins are used for the functionality of the theme
		$aScrips = array_merge($aScrips, array(
			//'js/jquery-1.10.2.min' 										=> array('*'), // Included in footer by default from google
			//'js/jqueryui-1.10.3.min' 										=> array('*'), // Included in footer by default from google
			'js/bootstrap.min' 												=> array('*'),
			'js/enquire' 													=> array('*'),
			'js/jquery.cookie' 												=> array('*'),
			'js/jquery.nicescroll.min'										=> array('*')
		));
		   
		//Following plugins can be removed based on usage
		//Used in multiple places
		$aScrips = array_merge($aScrips, array(
			'plugins/codeprettifier/prettify' 								=> array('*'), //Google Code Prettifier
			'plugins/easypiechart/jquery.easypiechart.min' 					=> array('*'),
			'plugins/sparklines/jquery.sparklines.min' 						=> array('*', 'charts-inline'),
			'plugins/form-toggle/toggle.min' 								=> array('*') // Toggle buttons
		));
		
		// Template specific scripts
		$aScrips = array_merge($aScrips, array(
			/** bblue scripts */
			'plugins/form-selectabletree/form-selectabletree.jquery'		=> array(
																				'rbac/add_role',
																				'rbac/add_permission'
																			),
			'plugins/makeconstant/makeconstant.jquery'						=> array('rbac/add_role','rbac/add_permission'),
		
			'plugins/jqueryui-timepicker/jquery.ui.timepicker.min' 			=> array('form-components'), // Time Picker. Requires jQuery UI
			
			/** Jqvmap */
			'plugins/jqvmap/jquery.vmap.min' 								=> array('maps-vector'),
			'plugins/jqvmap/maps/jquery.vmap.world' 						=> array('maps-vector'),
			'plugins/jqvmap/maps/jquery.vmap.europe' 						=> array('maps-vector'),
			'plugins/jqvmap/maps/jquery.vmap.usa' 							=> array('maps-vector'),
			'plugins/jqvmap/maps/jquery.vmap.sampledata'					=> array('maps-vector'),
			
			/** Dropzone */
			'plugins/dropzone/dropzone.min' 								=> array('form-dropzone', 'recipes/add'),
			
			/** Fullcalendar */
			'plugins/fullcalendar/fullcalendar.min' 						=> array('index'),
			
			/** Mixitup */
			'plugins/mixitup/jquery.mixitup.min' 							=> array('gallery'),
			
			/** Quicksearch */
			'plugins/quicksearch/jquery.quicksearch.min' 					=> array('form-components', 'rbac/add_role', 'rbac/add_permission'), // Quicksearch to go with Multisearch Plugin
		
			/** Form */
			'plugins/form-nestable/jquery.nestable.min' 					=> array('ui-nestable.php'),
			'plugins/form-nestable/app.min' 								=> array('ui-nestable.php'),
			'plugins/form-inputmask/jquery.inputmask.bundle.min' 			=> array('form-masks'),
			'plugins/form-parsley/parsley.min' 								=> array('form-validation'),
			'plugins/form-validation/jquery.validate' 						=> array('rbac/add_role','rbac/add_permission', 'recipes/add', 'extras-login'),
			'plugins/form-stepy/jquery.stepy' 								=> array('rbac/add_role','rbac/add_permission', 'recipes/add'),
			'plugins/form-multiselect/js/jquery.multi-select.min' 			=> array('form-components', 'rbac/add_role', 'rbac/add_permission'), // Multiselect Plugin
			'plugins/form-typeahead/typeahead.min' 							=> array('form-components', 'recipes/add'), // Typeahead for Autocomplete
			'plugins/form-select2/select2.min' 								=> array('form-components'), // Advanced Select Boxes
			'plugins/form-autosize/jquery.autosize-min' 					=> array('form-components', 'recipes/add'), // Autogrow Text Area
			'plugins/form-colorpicker/js/bootstrap-colorpicker.min' 		=> array('form-components'), // Color Picker
			'plugins/form-fseditor/jquery.fseditor-min' 					=> array('form-components', 'recipes/add'), // Fullscreen Editor
			'plugins/form-ckeditor/ckeditor' 								=> array('form-ckeditor', 'recipes/add'),	 // WYSIWYG CKEditor
			//'plugins/form-ckeditor/adapter/ckeditor' 						=> array('recipes/add'),	 // WYSIWYG CKEditor
			'plugins/form-xeditable/bootstrap3-editable/js/bootstrap-editable.min' => array('form-xeditable'),
			'plugins/form-daterangepicker/daterangepicker.min' 				=> array(					 // Date Range Picker
																				'form-components',
																				'index'
																			),
			'plugins/form-datepicker/js/bootstrap-datepicker'				=> array('recipes/add'),
			'plugins/form-daterangepicker/moment.min' 						=> array(					 // Moment.js for Date Range Picker
																				'form-components',
																				'form-xeditable',
																				'index'
																			),
			'plugins/form-tokenfield/bootstrap-tokenfield.min'				=> array('recipes/add'),

			/** Charts */
			'plugins/charts-flot/jquery.flot.min' 							=> array('index'),
			'plugins/charts-flot/jquery.flot.resize.min' 					=> array('index'),
			'plugins/charts-flot/jquery.flot.orderBars.min' 				=> array('index'),
			'plugins/charts-flot/jquery.flot.stack.min' 					=> array('charts-flot'),
			'plugins/charts-flot/jquery.flot.pie.min' 						=> array('charts-flot'),
			'plugins/charts-chartjs/Chart.min' 								=> array('charts-canvas'),
			'plugins/charts-morrisjs/raphael.min' 							=> array('charts-svg'),
			'plugins/charts-morrisjs/morris.min' 							=> array('charts-svg'),	
																			
			/** Demo */
			'demo/demo-index' 												=> array('index'),
			'demo/demo-gallery-simple' 										=> array('gallery'),
			'demo/demos-jqvmap' 											=> array('maps-vector'),
			'demo/demo-formcomponents' 										=> array('form-components'),
			'demo/demo-xeditable' 											=> array('form-xeditable'),
			'demo/demo-datatables' 											=> array('tables-data'),
			'demo/demo-flotgraph' 											=> array('charts-flot'),
			'demo/demo-formwizard' 											=> array('formwizard'),
			'demo/demo-tableeditable' 										=> array('tables-editable'),
			'demo/demo-calendar' 											=> array('calendar'),	
			'demo/demo-gmaps' 												=> array('maps-google'),
			'demo/demo-slider' 												=> array('ui-sliders'),
			'demo/demo-formvalidation' 										=> array('form-validation'),
			'demo/demo-mask' 												=> array('form-masks'),
			'demo/demo-datatables' 											=> array('tables-data'),
			'demo/demo-chartjs' 											=> array('charts-canvas'),
			'demo/demo-alerts' 												=> array('ui-alerts'),
			'demo/demo-inlinecharts' 										=> array('charts-inline'),
			'demo/demo-dualbox' 											=> array('form-duallistbox'),
			'demo/demo-nestable.min' 										=> array('ui-nestable.php'),
			'demo/demo-morrisjs' 											=> array('charts-svg'),
			'demo/demo-chatroom' 											=> array('extras-chatroom'),
			'demo/bblue-success' 											=> array('success'),
			'demo/bblue-rbac-add' 											=> array('rbac/add_role','rbac/add_permission'),
			'demo/bblue-recipes-add'										=> array('recipes/add'),
			'demo/bblue-login'												=> array('extras-login'),
																			
			/** Datatables */
			'plugins/datatables/dataTables.bootstrap' 						=> array('tables-data'),
			'plugins/datatables/TableTools' 								=> array('tables-editable'),
			'plugins/datatables/dataTables.editor' 							=> array('tables-editable'),
			'plugins/datatables/dataTables.editor.bootstrap' 				=> array('tables-editable'),
			'plugins/datatables/dataTables.bootstrap' 						=> array('tables-editable'),
			'plugins/datatables/jquery.dataTables.min' 						=> array(
																				'tables-data',
																				'tables-editable'
																			),
			
			/** Google maps */	
			'http://maps.google.com/maps/api/js?sensor=true&' 				=> array('maps-google'),	
			'plugins/gmaps/gmaps' 											=> array('maps-google'),
														
			/** Fullcalendar */	
			'plugins/fullcalendar/fullcalendar' 							=> array('calendar'),
																			
			/** Knob */																
			'plugins/knob/jquery.knob.min' 									=> array('ui-sliders', 'recipes/add'),
																			
			/** Progress-skylo */	
			'plugins/progress-skylo/skylo' 									=> array('ui-sliders'),
																																		
			/** Jquery fileupload */															
			'plugins/jquery-fileupload/js/vendor/jquery.ui.widget' 			=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/tmpl.min' 						=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/load-image.min' 					=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/canvas-to-blob.min' 				=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/jquery.blueimp-gallery.min' 		=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/jquery.fileupload' 				=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/jquery.fileupload-process' 		=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/jquery.fileupload-image' 			=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/jquery.fileupload-audio' 			=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/jquery.fileupload-video' 			=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/jquery.fileupload-validate' 		=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/jquery.fileupload-ui' 			=> array('form-fileupload'),
			'plugins/jquery-fileupload/js/main' 							=> array('form-fileupload'),

			/** Duallistbox */
			'plugins/duallistbox/jquery.bootstrap-duallistbox' 				=> array('form-duallistbox'),
			
			/** Jquery notify */
			'plugins/pines-notify/jquery.pnotify.min' 						=> array('ui-alerts'),

			/** Jquery pulsate*/
			'plugins/pulsate/jQuery.pulsate.min' 							=> array(
																				'ui-alerts',
																				'index'
																			),

			/** jQuery Star Rating */
			'plugins/jquery-star-rating/jquery.rating.pack'					=> array('recipes/add')
		));
		
		// Extras that are used in all template files
		$aScrips = array_merge($aScrips, array(
			'js/placeholdr' 												=> array('*'), // IE8 placeholders
			'js/application' 												=> array('*'),
			'demo/demo' 													=> array('*')
		));
		
		foreach($aScrips as $sScriptPath => $aPages)
		{
			if(in_array($sPage, $aPages) || in_array('*', $aPages))
			{
				$this->assign_block_vars('js', array(
					'PATH'		=> '/assets/' . $sScriptPath . '.js'
				));
			}
		}
	}
}