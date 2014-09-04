<?php
/**
 * table.php - generate table based on data & metadata
 */

/**
 * dataRow prints out a row of data
 * 
 * @param metadata - information about the data
 * @param row - a row of data
 * @param lookup - an associative array of lookup tables
 * @param action - name of the action button
 */
function dataRow($metadata, $row, $data, $last = false, $action = 'delete') {
	if (!isset($row['id'])) {
		foreach ($metadata as $col => $meta) {
			$row[$col] = "";
		}
		$row['id'] = 'new';
	} ?>
	<tr <?php if ($last) echo "id=last"; ?>>
		<td id='<?php echo $row['id'];?>'>
		<ul>
			<!-- bogus action; jquery initiates ajax posts for us -->
			<form id='<?php echo $row["id"];?>' action="bogus.php" method="GET">
<?php		foreach ($metadata as $col => $meta) { ?>
				<li class='<?php echo $col;?>'>

<?php			// Display column button if specified
				if (isset($meta['button']) && $row['id'] != 'new') { ?>
					<span class='button'>
						<input type='button' class='<?php echo $meta['button'];?>' value='<?php echo $meta['button'];?>'/>
					</span>
<?php			}

				// If a table lookup is specified, do the lookup,
				// otherwise just print out the row value directly
				$myval = '';
				if (isset($meta['table']) && $row['id'] != 'new') {
					$table = $data[$meta['table']]; // table to be looked up
					$key = $row[$meta['key']]; // fkey value in main table
					$fcol = $meta['col']; // foreign column name
					$myval = $table[$key][$fcol];
				} else {
					if (isset($row[$col])) {
						$myval = $row[$col];
					}
				} 
			
				// Higlights low inventory numbers
				if ($myval < 0 && isset($meta['negative'])) { ?>
					<span class='alert'
<?php			} elseif (isset($meta['low']) && $myval <= $meta['low']) { ?>
					<span class='warning'>
<?php			} else { ?>
					<span>
<?php			}

				// If this column is editable, enable click to edit which consists of
				// two span classes: text is the regular text normally shown and 
				// edit is the form display that's shown when editing
				// we use jquery to show/hide the edit/text spans ?>
				<span class='text'><?php echo $myval;?></span> <!-- duplicates span below in else -->
<?php			if (isset($meta['edit']) && $meta['edit']) { ?>
					<span class='edit'>
<?php				// if we're dealing with a table, display a pulldown with text set
					// to the value in the column specified by metadata
					if (isset($meta['table'])) {
						echo "<select name='$col'>";
						$list = $data[$meta['table']];
						foreach ($list as $k => $v) {
							echo "<option value='$k' name='".$v['name']."'";
							if ($k == $row[$meta['key']])
								echo " selected";
							echo ">".$v[$meta['col']]."</option>";
						}
						echo "</select>";
					} else { ?>
						<input type='text' class='<?php echo $col;?>' value='<?php echo $myval;?>' name='<?php echo $col;?>'/>
<?php				} ?>						
					</span>								
<?php			} ?>

				</span>
				</li>
<?php		} ?>
			<li class='action'>
				<span class='button'>
<?php			if ($row['id'] == 'new') { ?>
					<input type='button' value='add' class='add'/>
<?php			} else { ?>
					<input type='hidden' value='<?php echo $row['id'];?>' name='id'/>
					<input type='button' value='<?php echo $action; ?>' class='<?php echo $action; ?>'/>
<?php			} ?>
				</span>
				<span class='edit'>
					<input type='submit' value='submit' class='save'/>
					<input type='button' value='cancel' class='cancel'/>
				</span>
			</li>
			</form>
		</ul>
		</td>
	</tr> 
<?php	foreach ($metadata as $col => $meta) {
			if (isset($meta['subtable'])) {
				echo "<tr><td>SUBTABLE GOES HERE</td></tr>";
			}
		} ?>
<?php
}

/**
 * renderTable - displays a table of data with editable rows
 * @param metadata - describes the data column, title, and other features
 * @param data - the actual data to tabularize
 * @param edit - the table is editable if true
 * @param paginate - the number of rows per page, or 0 to disable pagination
 * @param action - name of the action button
 */
function renderTable($metadata, $data, $edit, $paginate, $action = 'delete') {
	if ($paginate > 0) {
		echo '<script type="text/javascript">numPerPage = ' . $paginate . ';</script>';
	} ?>
	<form><input id="searchInput" type="text" width="30" /></form>
	<table class='<?php if ($edit) echo "editable "; if ($paginate > 0) echo "paginated ";?>bordered filtered'>
		<thead>
			<tr>
				<th>
				<ul>
<?php	foreach ($metadata as $col => $val) {
			if (!isset($val['subtable'])) {
					echo "<li class='".$col."'>".$val['title']."</li>";
			}
		} ?>
					<li class='action'>Action</li>
				</ul>
				</th>
			</tr>
		</thead>
		<tbody>
<?php		foreach ($data['main'] as $row) {
				dataRow($metadata, $row, $data, false, $action);
			}
			dataRow($metadata, null, $data, true); // last row is for adding ?>
		</tbody>
	</table>
<?php
} // function renderTable



