@props(['src'])

<v-datagrid-column-manager src="{{ $src }}">
    <button class="transparent-button flex items-center gap-1 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
        <span class="icon-settings text-xl text-gray-600"></span>

        @lang('admin::app.components.datagrid.table.manage-columns')
    </button>
</v-datagrid-column-manager>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-column-manager-template"
    >
        <div
            class="relative"
            ref="manager"
            v-if="columns.length"
        >
            <div @click.stop="toggleDropdown">
                <slot></slot>
            </div>

            <div
                v-if="isOpen"
                class="absolute right-0 z-30 mt-2 w-64 rounded-lg border border-gray-200 bg-white p-3 shadow-lg dark:border-gray-800 dark:bg-gray-900"
            >
                <p class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-200">
                    @lang('admin::app.components.datagrid.table.visible-columns')
                </p>

                <div class="flex max-h-64 flex-col gap-2 overflow-auto text-sm">
                    <label
                        v-for="column in columns"
                        :key="column.index"
                        class="flex cursor-pointer items-center gap-2 text-gray-700 dark:text-gray-200"
                    >
                        <input
                            type="checkbox"
                            class="h-4 w-4 rounded border-gray-300 text-brandColor focus:ring-brandColor"
                            :checked="column.visibility"
                            @change="() => toggleColumn(column)"
                        >

                        <span v-html="column.label"></span>
                    </label>
                </div>

                <p
                    v-if="visibleCount <= 1"
                    class="mt-3 text-xs text-rose-600 dark:text-rose-400"
                >
                    @lang('admin::app.components.datagrid.table.min-columns-warning')
                </p>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-datagrid-column-manager', {
            template: '#v-datagrid-column-manager-template',

            props: ['src'],

            data() {
                return {
                    isOpen: false,
                    available: null,
                    columns: [],
                    visibilityKey: null,
                };
            },

            computed: {
                visibleCount() {
                    return this.columns.filter(column => column.visibility).length;
                },
            },

            mounted() {
                this.visibilityKey = `datagrid_columns_${this.src}`;
                this.registerEvents();
                document.addEventListener('click', this.handleOutsideClick);
            },

            beforeUnmount() {
                this.$emitter.off('change-datagrid', this.updateColumns);
                document.removeEventListener('click', this.handleOutsideClick);
            },

            methods: {
                registerEvents() {
                    this.$emitter.on('change-datagrid', this.updateColumns);
                },

                updateColumns(payload) {
                    if (payload?.src !== this.src) {
                        return;
                    }

                    this.available = payload.available;
                    this.columns = payload.available?.columns ?? [];

                    this.applyStoredVisibility();
                },

                toggleDropdown() {
                    this.isOpen = ! this.isOpen;
                },

                handleOutsideClick(event) {
                    if (! this.isOpen) {
                        return;
                    }

                    if (! this.$refs.manager?.contains(event.target)) {
                        this.isOpen = false;
                    }
                },

                toggleColumn(column) {
                    if (column.visibility && this.visibleCount <= 1) {
                        this.$emitter.emit('add-flash', {
                            type: 'warning',
                            message: "@lang('admin::app.components.datagrid.table.min-columns-warning')",
                        });

                        return;
                    }

                    column.visibility = ! column.visibility;

                    this.persistVisibility();
                },

                applyStoredVisibility() {
                    const stored = this.getStoredVisibility();

                    if (! stored) {
                        return;
                    }

                    this.columns.forEach(column => {
                        if (Object.prototype.hasOwnProperty.call(stored, column.index)) {
                            column.visibility = stored[column.index];
                        }
                    });
                },

                persistVisibility() {
                    const payload = {};

                    this.columns.forEach(column => {
                        payload[column.index] = column.visibility;
                    });

                    localStorage.setItem(this.visibilityKey, JSON.stringify(payload));
                },

                getStoredVisibility() {
                    try {
                        const raw = localStorage.getItem(this.visibilityKey);

                        return raw ? JSON.parse(raw) : null;
                    } catch (error) {
                        return null;
                    }
                },
            },
        });
    </script>
@endPushOnce
