<div>
	<cx-vui-list-table
		:is-empty="! itemsList.length"
		empty-message="<?php _e( 'No content types found', 'jet-engine' ); ?>"
	>
		<cx-vui-list-table-heading
			:slots="[ 'name', 'slug', 'table_name', 'actions' ]"
			class-name="cols-4"
			slot="heading"
		>
			<span slot="name"><?php _e( 'Content Type Name', 'jet-engine' ); ?></span>
			<span slot="slug"><?php _e( 'Slug', 'jet-engine' ); ?></span>
			<span slot="table_name"><?php _e( 'DB table name', 'jet-engine' ); ?></span>
			<span slot="actions"><?php _e( 'Actions', 'jet-engine' ); ?></span>
		</cx-vui-list-table-heading>
		<cx-vui-list-table-item
			:slots="[ 'name', 'slug', 'table_name', 'actions' ]"
			class-name="cols-4"
			slot="items"
			v-for="item in itemsList"
			:key="item.id"
		>
			<span slot="name">
				<a
					:href="getEditLink( item.id )"
					class="jet-engine-title-link"
				>{{ item.args.name }}</a>
			</span>
			<i slot="slug">{{ item.args.slug }}</i>
			<i slot="table_name">{{ prefix }}{{ item.args.slug }}</i>
			<div slot="actions">
				<a :href="getEditLink( item.id )"><?php _e( 'Edit', 'jet-engine' ); ?></a>
				|
				<a
					class="jet-engine-delete-item"
					href="#"
					@click.prevent="deleteItem( item )"
				><?php _e( 'Delete', 'jet-engine' ); ?></a>
			</div>
		</cx-vui-list-table-item>
	</cx-vui-list-table>
	<jet-cct-delete-dialog
		v-if="showDeleteDialog"
		v-model="showDeleteDialog"
		:item-id="deletedItem.id"
	></jet-cct-delete-dialog>
</div>