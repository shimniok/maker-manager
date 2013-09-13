<?php
/**
 * table.php - generate table based on data & metadata
 */

function dataRow2($metadata, $row, $data, $last = false) {
	if (!isset($row['id'])) {
		foreach ($metadata as $col => $meta) {
			$row[$col] = "";
		}
		$row['id'] = 'new';
	} ?>
	<tr <?php if ($last == true) echo "id=last" ?>>
		<td id='<?php echo $row['id'];?>'>
			<ul>
			<!-- bogus action; jquery initiates ajax posts for us -->
			<form action="bogus.php" method="GET">
<?php		foreach ($metadata as $col => $meta) {
				// Is there a subtable? Not sure wtf to do with subtable displays yet...
				if (!isset($meta['subtable'])) { ?>
				<li class='<?php echo $col;?>'>
<?php				// Display column button if specified
					if (isset($meta['button']) && $row['id'] != 'new') { ?>
						<span class='button'>
							<input type='button' class='<?php echo $meta['button'];?>' value='<?php echo $meta['button'];?>'/>
						</span>
<?php				}
					// If a table lookup is specified, do the lookup,
					// otherwise just print out the row value directly
					$myval = '';
					if (isset($meta['table']) && $row['id'] != 'new') {
						$table = $data[$meta['table']]; // table to be looked up
						$key = $row[$meta['key']]; // fkey value in main table
						$fcol = $meta['col']; // foreign column name
						$myval = $table[$key][$fcol];
					} else {
						$myval = $row[$col];
					} ?>

<?php				// If this column is editable, enable click to edit which consists of
					// two span classes: text is the regular text normally shown and 
					// edit is the form display that's shown when editing
					// we use jquery to show/hide the edit/text spans
					if ($meta['edit']) { ?>
						<span class='text'><?php echo $myval;?></span> <!-- duplicates span below in else -->
						<span class='edit'>
<?php					// if we're dealing with a table, display a pulldown with text set
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
<?php					} ?>						
						</span>								
<?php				} else { ?>					
						<span class='text'><?php echo $myval;?></span>
<?php				} ?>
				</li>
<?php		}
		} ?>
				<li class='action'>
					<span class='button'>
<?php				if ($row['id'] == 'new') { ?>
						<input type='button' value='add' class='add'/>
<?php				} else { ?>
						<input type='hidden' value='<?php echo $row['id'];?>' name='id'/>
						<input type='button' value='delete' class='delete'/>
<?php				} ?>
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

function renderTable2($metadata, $data) { ?>
	<table class='bordered'>
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
				dataRow2($metadata, $row, $data);
			}
			dataRow2($metadata, null, $data, true); // last row is for adding ?>
		</tbody>
	</table>
<?php
} // function renderTable



/**
 * dataRow prints out a row of data
 * 
 * @param metadata - information about the data
 * @param row - a row of data
 * @param lookup - an associative array of lookup tables
 */
function dataRow($metadata, $last = false, $row = NULL, $lookup = NULL) { 
	if (!isset($row['id'])) {
		foreach ($metadata as $col => $val) {
			$row[$col] = "";
		}
		$row['id'] = 'new';
	}
?>
			<tr <?php if ($last == true) echo "id=last" ?>>
				<td id='<?php echo $row['id'];?>'>
					<ul>
					<form action="test.php" method="GET">
<?php			foreach ($metadata as $col => $val) { ?>
						<li class='<?php echo $col;?>'>
<?php				if (isset($val['button']) && $row['id'] != 'new') { ?>
						<span class='button'>
							<input type='button' class='<?php echo $val['button'];?>' value='<?php echo $val['button'];?>'/>
						</span>
<?php				}
					$myval = '';
					if (isset($val['table'])) {
						if ($row['id'] != 'new') 
							$myval = $lookup[$val['table']][$row[$val['key']]];
					} else {
						$myval = $row[$col];
					} ?>
<?php				if ($val['edit']) { ?>
						<span class='text'><?php echo $myval;?></span>
						<span class='edit'>
<?php					if (isset($val['table'])) {
							echo "<select name='$col'>";
							$list = $lookup[$val['table']];
							foreach ($list as $i => $v) {
								echo "<option value='$i' name='$v'";
								if ($i == $row[$val['key']])
									echo " selected";
								echo ">$v</option>";
							}
							echo "</select>";
						} else { ?>
						<input type='text' class='<?php echo $col;?>' value='<?php echo $myval;?>' name='<?php echo $col;?>'/>
<?php					} ?>						
						</span>					
<?php				} else { ?>
						<?php echo $myval;?>
<?php				} ?>
						</li>
<?php			} ?>
						<li class='action'>
							<span class='button'>
<?php						if ($row['id'] == 'new') { ?>
								<input type='button' value='add' class='add'/>
<?php						} else { ?>
								<input type='hidden' value='<?php echo $row['id'];?>' name='id'/>
								<input type='button' value='delete' class='delete'/>
<?php						} ?>
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
<?php
}

/**
 * renderTable - displays a table of data with editable rows
 * @param metadata - describes the data column, title, and other features
 * @param data - the actual data to tabularize
 */
function renderTable($metadata, $data, $lookup = NULL) { ?>
	<div id="main">
	<table class='bordered'>
		<thead>
			<tr>
				<th>
				<ul>
<?php	foreach ($metadata as $col => $val) {
					echo "<li class='".$col."'>".$val['title']."</li>";
		} ?>
					<li class='action'>Action</li>
				</ul>
				</th>
			</tr>
		</thead>
		<tbody>
<?php		foreach ($data as $row) {
				dataRow($metadata, false, $row, $lookup);
			} 
			dataRow($metadata, true, null, $lookup); ?>
		</tbody>
	</table>
	</div>
<?php
} // function renderTable

