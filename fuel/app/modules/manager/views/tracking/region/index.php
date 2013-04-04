<h2>Listing Tracking_regions</h2>
<br>
<?php if ($tracking_regions): ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($tracking_regions as $tracking_region): ?>		<tr>
		<td>

			<?php echo $tracking_region->name; ?>
		</td>
			<td>
				<?php echo Html::anchor('tracking/region/view/'.$tracking_region->id, 'View'); ?> |
				<?php echo Html::anchor('tracking/region/edit/'.$tracking_region->id, 'Edit'); ?> |
				<?php echo Html::anchor('tracking/region/delete/'.$tracking_region->id, 'Delete', array('onclick' => "return confirm('Are you sure?')")); ?>

			</td>
		</tr>
<?php endforeach; ?>	</tbody>
</table>

<?php else: ?>
<p>No Tracking_regions.</p>

<?php endif; ?><p>
	<?php echo Html::anchor('tracking/region/create', 'Add new Tracking region', array('class' => 'btn btn-success')); ?>

</p>
