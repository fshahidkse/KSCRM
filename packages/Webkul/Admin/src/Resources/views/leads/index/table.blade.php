{!! view_render_event('admin.leads.index.table.before') !!}

<div class="overflow-x-auto overflow-y-visible rounded-lg border border-transparent">
    <x-admin::datagrid :src="route('admin.leads.index')" class="min-w-full relative z-0">
        <!-- DataGrid Shimmer -->
        <x-admin::shimmer.datagrid />

        <x-slot:toolbar-right-after>
            @include('admin::leads.index.view-switcher')
        </x-slot>
    </x-admin::datagrid>
</div>

{!! view_render_event('admin.leads.index.table.after') !!}
