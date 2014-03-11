<?php
namespace App;

if(IN_CONTROLLER !== true){ die((IS_DEVELOPMENT_AREA === true) ? ('Not in controller. Error at ' . __FILE__ . '.') : ''); }
/*
		NOTE!!!
		This file contains the code that allows you to use phpBB's Template Engine outside
		of the phpBB framework in your own site. This is allowed because the phpBB code is
		released under the GPL license.
		
		This was not my idea, all I have done is consolidated all of the code into one file
		to make it easier on everyone else.
		The original creator of this idea was nanothree on phpBB.com
		Here's the topic: http://www.phpbb.com/community/viewtopic.php?f=71&t=1557455
		
		This file was downloaded in October 2010 by Aleksander Lanes and has been redesigned to fit in with the Ruby CMS
*/

/**
* Base Template class.
* @package phpBB3
*/
final class Template
{
	/** variable that holds all the data we'll be substituting into
	* the compiled templates. Takes form:
	* --> $this->_tpldata[block][iteration#][child][iteration#][child2][iteration#][variablename] == value
	* if it's a root-level variable, it'll be like this:
	* --> $this->_tpldata[.][0][varname] == value
	*/
	var $_tpldata = array('.' => array(0 => array()));
	var $_rootref;

	// Root dir and hash of filenames for each template handle.
	var $root = '';
	var $cachepath = '';
	var $files = array();
	var $filename = array();
	var $files_inherit = array();
	var $files_template = array();
	var $inherit_root = '';
	var $orig_tpl_storedb;
	var $orig_tpl_inherits_id;
	
	// Hack by AL 20.12
	private $bHasShownTemplateVars;

	// this will hash handle names to the compiled/uncompiled code for that handle.
	var $compiled_code = array();
	
	/**
	* Set template location
	* @access public
	*/
	function set_template()
	{

		if (file_exists($phpbb_root_path . 'styles/' . $user->theme['template_path'] . '/template'))
		{
			$this->root = $phpbb_root_path . 'styles/' . $user->theme['template_path'] . '/template';
			$this->cachepath = $phpbb_root_path . 'cache/tpl_' . str_replace('_', '-', $user->theme['template_path']) . '_';

			if ($this->orig_tpl_storedb === null)
			{
				$this->orig_tpl_storedb = $user->theme['template_storedb'];
			}

			if ($this->orig_tpl_inherits_id === null)
			{
				$this->orig_tpl_inherits_id = $user->theme['template_inherits_id'];
			}

			$user->theme['template_storedb'] = $this->orig_tpl_storedb;
			$user->theme['template_inherits_id'] = $this->orig_tpl_inherits_id;

			if ($user->theme['template_inherits_id'])
			{
				$this->inherit_root = $phpbb_root_path . 'styles/' . $user->theme['template_inherit_path'] . '/template';
			}
		}
		else
		{
			trigger_error('Template path could not be found: styles/' . $user->theme['template_path'] . '/template', E_USER_ERROR);
		}

		$this->_rootref = &$this->_tpldata['.'][0];

		return true;
	}

	/**
	* Set custom template location (able to use directory outside of phpBB)
	* @access public
	*/
	function set_custom_template($template_path, $template_name, $template_mode = 'template')
	{

		// Make sure $template_path has no ending slash
		if (substr($template_path, -1) == '/')
		{
			$template_path = substr($template_path, 0, -1);
		}

		$this->root = $template_path;

		// As the template-engine is used for more than the template (emails, etc.), we should not set $user->theme in all cases, but only on the real template.
		if ($template_mode == 'template')
		{
			$user->theme['template_storedb'] = false;
			$user->theme['template_inherits_id'] = false;
		}

		//Hack by AL: Commented the line below. Only requried for rev2...  
		//$this->_rootref = &$this->_tpldata['.'][0];

		return true;
	}

	/**
	* Sets the template filenames for handles. $filename_array
	* should be a hash of handle => filename pairs.
	* @access public
	*/
	function set_filenames($filename_array)
	{
		if (!is_array($filename_array))
		{
			return false;
		}
		foreach ($filename_array as $handle => $filename)
		{
			if (empty($filename))
			{
				trigger_error("template->set_filenames: Empty filename specified for $handle", E_USER_ERROR);
			}

			$this->filename[$handle] = $filename;

			$this->files[$handle] = $this->root . '/' . $filename;

			if ($this->inherit_root)
			{
				$this->files_inherit[$handle] = $this->inherit_root . '/' . $filename;
			}
		}

		return true;
	}

	/**
	* Destroy template data set
	* @access public
	*/
	function destroy()
	{
		$this->_tpldata = array('.' => array(0 => array()));
		$this->_rootref = &$this->_tpldata['.'][0];
	}

	/**
	* Reset/empty complete block
	* @access public
	*/
	function destroy_block_vars($blockname)
	{
		if (strpos($blockname, '.') !== false)
		{
			// Nested block.
			$blocks = explode('.', $blockname);
			$blockcount = sizeof($blocks) - 1;

			$str = &$this->_tpldata;
			for ($i = 0; $i < $blockcount; $i++)
			{
				$str = &$str[$blocks[$i]];
				$str = &$str[sizeof($str) - 1];
			}

			unset($str[$blocks[$blockcount]]);
		}
		else
		{
			// Top-level block.
			unset($this->_tpldata[$blockname]);
		}

		return true;
	}

	/**
	* Display handle
	* @access public
	*/
	function display($handle, $include_once = true)
	{
		if ($filename = $this->_tpl_load($handle))
		{
			($include_once) ? include_once($filename) : include($filename);
		}
		else
		{
			//Hack by AL
			//$this->lang = $this->registry->getObject('lang');
			
			// This is the function that does all the magic!
			ob_start(function($b){return preg_replace(array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s'),array('>','<','\\1'),$b);});
			eval(' ?>' . $this->compiled_code[$handle] . '<?php ');
			
			// Hack by AL: print the array for bug fixing		{
			if(PRINT_TEMPLATE_VARS === true && !$this->bHasShownTemplateVars)
			{
				$this->bHasShownTemplateVars = true;
				echo '<pre>'; print_r($this->_rootref); echo '</pre>';// @todo Her m� det lages en funksjon som returnerer $this->_rootref for bruk i Debug modulen:
			}
		}

		return true;
	}

	/**
	* Display the handle and assign the output to a template variable or return the compiled result.
	* @access public
	*/
	function assign_display($handle, $template_var = '', $include_once = false)
	{
		ob_start();
		$this->display($handle, $include_once);
		$contents = ob_get_clean();

		$this->assign_var($template_var, $contents);

		return true;
	}

	/**
	* Load a compiled template if possible, if not, recompile it
	* @access private
	*/
	function _tpl_load(&$handle)
	{
		global $user, $phpEx, $config;

		if (!isset($this->filename[$handle]))
		{
			// Hack by AL:
			//trigger_error("template->_tpl_load(): No file specified for handle $handle", E_USER_ERROR);
			throw new \Exception("template->_tpl_load(): No file specified for handle $handle");
		}

		$filename = $this->cachepath . str_replace('/', '.', $this->filename[$handle]) . '.' . $phpEx;
		//$this->files_template[$handle] = (isset($user->theme['template_id'])) ? $user->theme['template_id'] : 0;

		$recompile = false;
		if (!file_exists($filename) || @filesize($filename) === 0)
		{
			$recompile = true;
		}
		else if ($config['load_tplcompile'])
		{
			// No way around it: we need to check inheritance here
			if ($user->theme['template_inherits_id'] && !file_exists($this->files[$handle]))
			{
				$this->files[$handle] = $this->files_inherit[$handle];
				$this->files_template[$handle] = $user->theme['template_inherits_id'];
			}
			$recompile = (@filemtime($filename) < filemtime($this->files[$handle])) ? true : false;
		}

		// Recompile page if the original template is newer, otherwise load the compiled version
		if (!$recompile)
		{
			return $filename;
		}

		//Hack by AL @ 3.9.2013
		// Fjernet linjene under
		//global $db, $phpbb_root_path;

		//if (!class_exists('template_compile'))
		//{
		//	include($phpbb_root_path . 'includes/functions_template.' . $phpEx);
		//}

		// Inheritance - we point to another template file for this one. Equality is also used for store_db
		if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'] && !file_exists($this->files[$handle]))
		{
			$this->files[$handle] = $this->files_inherit[$handle];
			$this->files_template[$handle] = $user->theme['template_inherits_id'];
		}

		$compile = new Template_compile($this);

		// If we don't have a file assigned to this handle, die.
		if (!isset($this->files[$handle]))
		{
			trigger_error("template->_tpl_load(): No file specified for handle $handle", E_USER_ERROR);
		}

		// Just compile if no user object is present (happens within the installer)
		if (!$user)
		{
			$compile->_tpl_load_file($handle);
			return false;
		}

		if (isset($user->theme['template_storedb']) && $user->theme['template_storedb'])
		{
			$rows = array();
			$ids = array();
			// Inheritance
			if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'])
			{
				$ids[] = $user->theme['template_inherits_id'];
			}
			$ids[] = $user->theme['template_id'];

			foreach ($ids as $id)
			{
				$sql = 'SELECT *
				FROM ' . STYLES_TEMPLATE_DATA_TABLE . '
				WHERE template_id = ' . $id . "
					AND (template_filename = '" . $db->sql_escape($this->filename[$handle]) . "'
						OR template_included " . $db->sql_like_expression($db->any_char . $this->filename[$handle] . ':' . $db->any_char) . ')';

				$result = $db->sql_query($sql);
				while ($row = $db->sql_fetchrow($result))
				{
					$rows[$row['template_filename']] = $row;
				}
				$db->sql_freeresult($result);
			}

			if (sizeof($rows))
			{
				foreach ($rows as $row)
				{
					$file = $this->root . DIRECTORY_SEPARATOR . $row['template_filename'];
					$force_reload = false;
					if ($row['template_id'] != $user->theme['template_id'])
					{
						// make sure that we are not overlooking a file not in the db yet
						if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'] && !file_exists($file))
						{
							$file = $this->inherit_root . DIRECTORY_SEPARATOR . $row['template_filename'];
							$this->files[$row['template_filename']] = $file;
							$this->files_inherit[$row['template_filename']] = $file;
							$this->files_template[$row['template_filename']] = $user->theme['template_inherits_id'];
						}
						else if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'])
						{
							// Ok, we have a situation. There is a file in the subtemplate, but nothing in the DB. We have to fix that.
							$force_reload = true;
							$this->files_template[$row['template_filename']] = $user->theme['template_inherits_id'];
						}
					}
					else
					{
						$this->files_template[$row['template_filename']] = $user->theme['template_id'];
					}

					if ($force_reload || $row['template_mtime'] < filemtime($file))
					{
						if ($row['template_filename'] == $this->filename[$handle])
						{
							$compile->_tpl_load_file($handle, true);
						}
						else
						{
							$this->files[$row['template_filename']] = $file;
							$this->filename[$row['template_filename']] = $row['template_filename'];
							$compile->_tpl_load_file($row['template_filename'], true);
							unset($this->compiled_code[$row['template_filename']]);
							unset($this->files[$row['template_filename']]);
							unset($this->filename[$row['template_filename']]);
						}
					}

					if ($row['template_filename'] == $this->filename[$handle])
					{
						$this->compiled_code[$handle] = $compile->compile(trim($row['template_data']));
						$compile->compile_write($handle, $this->compiled_code[$handle]);
					}
					else
					{
						// Only bother compiling if it doesn't already exist
						if (!file_exists($this->cachepath . str_replace('/', '.', $row['template_filename']) . '.' . $phpEx))
						{
							$this->filename[$row['template_filename']] = $row['template_filename'];
							$compile->compile_write($row['template_filename'], $compile->compile(trim($row['template_data'])));
							unset($this->filename[$row['template_filename']]);
						}
					}
				}
			}
			else
			{
				$file = $this->root . DIRECTORY_SEPARATOR . $row['template_filename'];

				if (isset($user->theme['template_inherits_id']) && $user->theme['template_inherits_id'] && !file_exists($file))
				{
					$file = $this->inherit_root . DIRECTORY_SEPARATOR . $row['template_filename'];
					$this->files[$row['template_filename']] = $file;
					$this->files_inherit[$row['template_filename']] = $file;
					$this->files_template[$row['template_filename']] = $user->theme['template_inherits_id'];
				}
				// Try to load from filesystem and instruct to insert into the styles table...
				$compile->_tpl_load_file($handle, true);
				return false;
			}

			return false;
		}

		$compile->_tpl_load_file($handle);
		return false;
	}

	/**
	* Assign key variable pairs from an array
	* @access public
	*/
	function assign_vars($vararray)
	{
		foreach ($vararray as $key => $val)
		{
			$this->_rootref[$key] = $val;
		}

		return true;
	}

	/**
	* Assign a single variable to a single key
	* @access public
	*/
	function assign_var($varname, $varval)
	{
		$this->_rootref[$varname] = $varval;
		
		return true;
	}

	/**
	* Assign key variable pairs from an array to a specified block
	* @access public
	*/
	function assign_block_vars($blockname, $vararray)
	{
		if (strpos($blockname, '.') !== false)
		{
			// Nested block.
			$blocks = explode('.', $blockname);
			$blockcount = sizeof($blocks) - 1;

			$str = &$this->_tpldata;
			for ($i = 0; $i < $blockcount; $i++)
			{
				$str = &$str[$blocks[$i]];
				$str = &$str[sizeof($str) - 1];
			}

			$s_row_count = isset($str[$blocks[$blockcount]]) ? sizeof($str[$blocks[$blockcount]]) : 0;
			$vararray['S_ROW_COUNT'] = $s_row_count;

			// Assign S_FIRST_ROW
			if (!$s_row_count)
			{
				$vararray['S_FIRST_ROW'] = true;
			}

			// Now the tricky part, we always assign S_LAST_ROW and remove the entry before
			// This is much more clever than going through the complete template data on display (phew)
			$vararray['S_LAST_ROW'] = true;
			if ($s_row_count > 0)
			{
				unset($str[$blocks[$blockcount]][($s_row_count - 1)]['S_LAST_ROW']);
			}

			// Now we add the block that we're actually assigning to.
			// We're adding a new iteration to this block with the given
			// variable assignments.
			$str[$blocks[$blockcount]][] = $vararray;
		}
		else
		{
			// Top-level block.
			$s_row_count = (isset($this->_tpldata[$blockname])) ? sizeof($this->_tpldata[$blockname]) : 0;
			$vararray['S_ROW_COUNT'] = $s_row_count;

			// Assign S_FIRST_ROW
			if (!$s_row_count)
			{
				$vararray['S_FIRST_ROW'] = true;
			}

			// We always assign S_LAST_ROW and remove the entry before
			$vararray['S_LAST_ROW'] = true;
			if ($s_row_count > 0)
			{
				unset($this->_tpldata[$blockname][($s_row_count - 1)]['S_LAST_ROW']);
			}

			// Add a new iteration to this block with the variable assignments we were given.
			$this->_tpldata[$blockname][] = $vararray;
		}

		return true;
	}

	/**
	* Change already assigned key variable pair (one-dimensional - single loop entry)
	*
	* An example of how to use this function:
	* {@example alter_block_array.php}
	*
	* @param	string	$blockname	the blockname, for example 'loop'
	* @param	array	$vararray	the var array to insert/add or merge
	* @param	mixed	$key		Key to search for
	*
	* array: KEY => VALUE [the key/value pair to search for within the loop to determine the correct position]
	*
	* int: Position [the position to change or insert at directly given]
	*
	* If key is false the position is set to 0
	* If key is true the position is set to the last entry
	*
	* @param	string	$mode		Mode to execute (valid modes are 'insert' and 'change')
	*
	*	If insert, the vararray is inserted at the given position (position counting from zero).
	*	If change, the current block gets merged with the vararray (resulting in new key/value pairs be added and existing keys be replaced by the new value).
	*
	* Since counting begins by zero, inserting at the last position will result in this array: array(vararray, last positioned array)
	* and inserting at position 1 will result in this array: array(first positioned array, vararray, following vars)
	*
	* @return bool false on error, true on success
	* @access public
	*/
	function alter_block_array($blockname, $vararray, $key = false, $mode = 'insert')
	{
		if (strpos($blockname, '.') !== false)
		{
			// Nested blocks are not supported
			return false;
		}

		// Change key to zero (change first position) if false and to last position if true
		if ($key === false || $key === true)
		{
			$key = ($key === false) ? 0 : sizeof($this->_tpldata[$blockname]);
		}

		// Get correct position if array given
		if (is_array($key))
		{
			// Search array to get correct position
			list($search_key, $search_value) = @each($key);

			$key = NULL;
			foreach ($this->_tpldata[$blockname] as $i => $val_ary)
			{
				if ($val_ary[$search_key] === $search_value)
				{
					$key = $i;
					break;
				}
			}

			// key/value pair not found
			if ($key === NULL)
			{
				return false;
			}
		}

		// Insert Block
		if ($mode == 'insert')
		{
			// Make sure we are not exceeding the last iteration
			if ($key >= sizeof($this->_tpldata[$blockname]))
			{
				$key = sizeof($this->_tpldata[$blockname]);
				unset($this->_tpldata[$blockname][($key - 1)]['S_LAST_ROW']);
				$vararray['S_LAST_ROW'] = true;
			}
			else if ($key === 0)
			{
				unset($this->_tpldata[$blockname][0]['S_FIRST_ROW']);
				$vararray['S_FIRST_ROW'] = true;
			}

			// Re-position template blocks
			for ($i = sizeof($this->_tpldata[$blockname]); $i > $key; $i--)
			{
				$this->_tpldata[$blockname][$i] = $this->_tpldata[$blockname][$i-1];
				$this->_tpldata[$blockname][$i]['S_ROW_COUNT'] = $i;
			}

			// Insert vararray at given position
			$vararray['S_ROW_COUNT'] = $key;
			$this->_tpldata[$blockname][$key] = $vararray;

			return true;
		}

		// Which block to change?
		if ($mode == 'change')
		{
			if ($key == sizeof($this->_tpldata[$blockname]))
			{
				$key--;
			}

			$this->_tpldata[$blockname][$key] = array_merge($this->_tpldata[$blockname][$key], $vararray);
			return true;
		}

		return false;
	}

	/**
	* Include a separate template
	* @access private
	*/
	function _tpl_include($filename, $include = true)
	{
		$handle = $filename;
		$this->filename[$handle] = $filename;
		$this->files[$handle] = $this->root . '/' . $filename;
		if ($this->inherit_root)
		{
			$this->files_inherit[$handle] = $this->inherit_root . '/' . $filename;
		}
		
		$filename = $this->_tpl_load($handle);

		if ($include)
		{
			if ($filename)
			{
				include($filename);
				return;
			}
			eval(' ?>' . $this->compiled_code[$handle] . '<?php ');
		}
	}

	/**
	* Include a php-file
	* @access private
	*/
	function _php_include($filename)
	{
		global $phpbb_root_path;

		$file = $phpbb_root_path . $filename;

		if (!file_exists($file))
		{
			// trigger_error cannot be used here, as the output already started
			echo 'template->_php_include(): File ' . htmlspecialchars($file) . ' does not exist or is empty';
			return;
		}
		include($file);
	}
}