@props(['isMultiRow' => false])

<v-datagrid-table
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @selectAll="selectAll"
    @sort="sort"
    @actionSuccess="get"
>
    {{ $slot }}
</v-datagrid-table>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-table-template"
    >
        <div class="w-full">
            <!-- Floating horizontal scrollbar -->
            <div
                v-show="showFloatingScroll"
                class="pointer-events-none fixed z-30"
                :style="floatingScrollWrapperStyles"
            >
                <div
                    class="datagrid-horizontal-scroll mb-2 h-4 w-full overflow-x-auto overflow-y-hidden rounded border border-gray-200 bg-white pointer-events-auto dark:border-gray-800 dark:bg-gray-900"
                    ref="horizontalScroll"
                    @scroll="handleHorizontalScroll"
                >
                    <div :style="{ width: `${scrollWidth}px`, height: '1px' }"></div>
                </div>
            </div>

            <!-- Table view for larger screens, Card view for mobile -->
            <div class="table-responsive box-shadow rounded-t-0 w-full border border-gray-200 dark:border-gray-800">
                <div
                    class="w-full overflow-x-auto bg-white dark:bg-gray-900"
                    ref="scrollContainer"
                    @scroll="handleTableScroll"
                >
                <!-- Table Header - Always visible on all screens -->
                <slot
                    name="header"
                    :is-loading="isLoading"
                    :available="available"
                    :applied="applied"
                    :select-all="selectAll"
                    :sort="sort"
                    :perform-action="performAction"
                >
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.head :isMultiRow="$isMultiRow" />
                    </template>

                    <template v-else>
                        <div class="inline-block min-w-full">
                        <div
                            class="row grid min-h-[47px] items-center gap-0 border-b bg-gray-50 px-4 py-2.5 text-black dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 max-lg:hidden sticky top-16 z-10 shadow-md"
                            :style="gridTemplateStyle"
                        >
                            <!-- Mass Actions -->
                            <p v-if="available.massActions.length">
                                <label for="mass_action_select_all_records">
                                    <input
                                        type="checkbox"
                                        name="mass_action_select_all_records"
                                        id="mass_action_select_all_records"
                                        class="peer hidden"
                                        :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                        @change="selectAll"
                                    >

                                    <span
                                        class="icon-checkbox-outline cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor"
                                        :class="[
                                            applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checkbox-select peer-checked:text-brandColor ' : (
                                                applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-multiple peer-checked:brandColor' : ''
                                            ),
                                        ]"
                                    >
                                    </span>
                                </label>
                            </p>

                            <!-- Columns -->
                            <template v-for="column in available.columns">
                                <div
                                    class="relative flex items-center gap-1.5 break-words pr-3"
                                    :class="{'cursor-pointer select-none hover:text-gray-800 dark:hover:text-white': column.sortable}"
                                    @click="sort(column)"
                                    v-if="column.visibility"
                                > 
                                    <p v-html="column.label"></p>

                                    <i
                                        class="align-text-bottom text-base text-gray-600 dark:text-gray-300"
                                        :class="[applied.sort.order === 'asc' ? 'icon-stats-down': 'icon-stats-up']"
                                        v-if="column.index == applied.sort.column"
                                    ></i>

                                    <span
                                        class="absolute right-0 top-0 h-full w-[6px] cursor-col-resize select-none bg-transparent transition hover:bg-gray-400 dark:hover:bg-gray-600"
                                        @mousedown.stop.prevent="startResize($event, column)"
                                    ></span>
                                </div>
                            </template>

                            <!-- Actions -->
                            <p
                                class="flex items-center justify-center border-l border-gray-200 px-2 dark:border-gray-800"
                                v-if="available.actions.length"
                            >
                                @lang('admin::app.components.datagrid.table.actions')
                            </p>
                        </div>
                        </div>
                        
                        <!-- Mobile Sort/Filter Header -->
                        <div class="hidden border-b bg-gray-50 px-4 py-3 text-black dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 max-lg:block">
                            <div class="flex items-center justify-between">
                                <!-- Mass Actions for Mobile -->
                                <div v-if="available.massActions.length">
                                    <label for="mass_action_select_all_records">
                                        <input
                                            type="checkbox"
                                            name="mass_action_select_all_records"
                                            id="mass_action_select_all_records"
                                            class="peer hidden"
                                            :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                            @change="selectAll"
                                        >
    
                                        <span
                                            class="icon-checkbox-outline cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor"
                                            :class="[
                                                applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checkbox-select peer-checked:text-brandColor ' : (
                                                    applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-multiple peer-checked:brandColor' : ''
                                                ),
                                            ]"
                                        >
                                        </span>
                                    </label>
                                </div>
                                
                                <!-- Mobile Sort Dropdown -->
                                <div class="flex w-full justify-end" v-if="available.columns.some(column => column.sortable)">
                                    <x-admin::dropdown position="bottom-{{ in_array(app()->getLocale(), ['fa', 'ar']) ? 'left' : 'right' }}">
                                        <x-slot:toggle>
                                            <div class="flex items-center gap-1">
                                                <button
                                                    type="button"
                                                    class="inline-flex w-full max-w-max cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                >
                                                    <span>
                                                        Sort
                                                    </span>
                    
                                                    <span class="icon-down-arrow text-2xl"></span>
                                                </button>
                                            </div>
                                        </x-slot>
                
                                        <x-slot:menu>
                                            <x-admin::dropdown.menu.item
                                                v-for="column in available.columns.filter(column => column.sortable && column.visibility)"
                                                @click="sort(column)"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span v-html="column.label"></span>
                                                    <i
                                                        class="align-text-bottom text-base text-gray-600 dark:text-gray-300"
                                                        :class="[applied.sort.order === 'asc' ? 'icon-stats-down': 'icon-stats-up']"
                                                        v-if="column.index == applied.sort.column"
                                                    ></i>
                                                </div>
                                            </x-admin::dropdown.menu.item>
                                        </x-slot>
                                    </x-admin::dropdown>
                                </div>
                            </div>
                        </div>
                    </template>
                </slot>

                <slot
                    name="body"
                    :is-loading="isLoading"
                    :available="available"
                    :applied="applied"
                    :select-all="selectAll"
                    :sort="sort"
                    :perform-action="performAction"
                >
                    <template v-if="isLoading">
                        <x-admin::shimmer.datagrid.table.body :isMultiRow="$isMultiRow" />
                    </template>

                    <template v-else>
                        <template v-if="available.records.length">
                            <!-- Desktop View -->
                            <div class="inline-block min-w-full" v-for="record in available.records">
                            <div
                                class="row grid items-center gap-0 border-b px-4 py-4 text-black transition-all hover:bg-gray-100 dark:border-gray-800 dark:text-gray-300 dark:hover:bg-gray-800 max-lg:hidden"
                                :style="gridTemplateStyle"
                            >
                                <!-- Mass Actions -->
                                <p v-if="available.massActions.length">
                                    <label :for="`mass_action_select_record_${record[available.meta.primary_column]}`">
                                        <input
                                            type="checkbox"
                                            :name="`mass_action_select_record_${record[available.meta.primary_column]}`"
                                            :value="record[available.meta.primary_column]"
                                            :id="`mass_action_select_record_${record[available.meta.primary_column]}`"
                                            class="peer hidden"
                                            v-model="applied.massActions.indices"
                                        >

                                        <span class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor">
                                        </span>
                                    </label>
                                </p>

                                <!-- Columns -->
                                <template v-for="column in available.columns">
                                    <p
                                        class="break-words whitespace-normal min-w-0 px-1.5"
                                        v-html="record[column.index]"
                                        v-if="column.visibility"
                                    >
                                    </p>
                                </template>

                                <!-- Actions -->
                                <p
                                    class="flex items-center justify-center gap-x-1.5 min-w-0 border-l border-gray-200 px-2 dark:border-gray-800"
                                    v-if="available.actions.length"
                                >
                                    <span
                                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"
                                        :class="action.icon"
                                        :title="action.title"
                                        v-text="! action.icon ? action.title : ''"
                                        v-for="action in record.actions"
                                        @click="performAction(action)"
                                    >
                                    </span>
                                </p>
                            </div>
                            </div>
                            
                            <!-- Mobile Card View -->
                            <div
                                class="hidden border-b px-4 py-4 text-black dark:border-gray-800 dark:text-gray-300 max-lg:block"
                                v-for="record in available.records"
                            >
                                <div class="mb-2 flex items-center justify-between">
                                    <!-- Mass Actions for Mobile Cards -->
                                    <div class="flex w-full items-center justify-between gap-2">
                                        <p v-if="available.massActions.length">
                                            <label :for="`mass_action_select_record_${record[available.meta.primary_column]}`">
                                                <input
                                                    type="checkbox"
                                                    :name="`mass_action_select_record_${record[available.meta.primary_column]}`"
                                                    :value="record[available.meta.primary_column]"
                                                    :id="`mass_action_select_record_${record[available.meta.primary_column]}`"
                                                    class="peer hidden"
                                                    v-model="applied.massActions.indices"
                                                >
        
                                                <span class="icon-checkbox-outline peer-checked:icon-checkbox-select cursor-pointer rounded-md text-2xl text-gray-500 peer-checked:text-brandColor">
                                                </span>
                                            </label>
                                        </p>

                                        <!-- Actions for Mobile -->
                                        <div
                                            class="flex w-full items-center justify-end"
                                            v-if="available.actions.length"
                                        >
                                            <span
                                                class="dark:hover:bg-gray-80 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200"
                                                :class="action.icon"
                                                v-text="! action.icon ? action.title : ''"
                                                v-for="action in record.actions"
                                                @click="performAction(action)"
                                            >
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="grid gap-2">
                                    <template v-for="column in available.columns">
                                        <div class="flex flex-wrap items-baseline gap-x-2">
                                            <span class="text-slate-600 dark:text-gray-300" v-html="column.label + ':'"></span>
                                            <span class="break-words font-medium text-slate-900 dark:text-white" v-html="record[column.index]"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template v-else>
                            <div class="row grid border-b px-4 py-4 text-center text-gray-600 dark:border-gray-800 dark:text-gray-300">
                                <p>
                                    @lang('admin::app.components.datagrid.table.no-records-available')
                                </p>
                            </div>
                        </template>
                    </template>
                </slot>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-datagrid-table', {
            template: '#v-datagrid-table-template',

            props: ['isLoading', 'available', 'applied'],

            data() {
                return {
                    columnWidths: {},
                    resizingColumn: null,
                    resizeStartX: 0,
                    resizeStartWidth: 0,
                    scrollWidth: 0,
                    showHorizontalScroll: false,
                    isSyncingHorizontalScroll: false,
                    showFloatingScroll: false,
                    floatingScrollWrapperStyles: {
                        left: '0px',
                        bottom: '24px',
                        width: '0px',
                    },
                };
            },

            computed: {
                gridTemplateStyle() {
                    const widths = [];
                    let totalWidth = 0;

                    if (this.available.massActions.length) {
                        widths.push('48px');
                        totalWidth += 48;
                    }

                    this.available.columns.forEach((column) => {
                        if (! column.visibility) {
                            return;
                        }

                        const width = this.columnWidths[column.index] ?? column.width ?? 160;

                        const normalizedWidth = Math.max(120, width);

                        widths.push(`${normalizedWidth}px`);

                        totalWidth += normalizedWidth;
                    });

                    if (this.available.actions.length) {
                        widths.push('120px');

                        totalWidth += 120;
                    }

                    if (! widths.length) {
                        widths.push('1fr');
                    }

                    return `grid-template-columns: ${widths.join(' ')};`;
                },

                gridsCount() {
                    let count = this.available.columns.filter((column) => column.visibility).length;

                    if (this.available.actions.length) {
                        ++count;
                    }

                    if (this.available.massActions.length) {
                        ++count;
                    }

                    return count;
                },
            },

            mounted() {
                this.ensureColumnWidths();
                this.updateHorizontalScrollState();

                window.addEventListener('resize', this.updateHorizontalScrollState);
                window.addEventListener('scroll', this.handleWindowScroll, { passive: true });
            },

            watch: {
                'available.columns': {
                    handler() {
                        this.ensureColumnWidths();
                        this.updateHorizontalScrollState();
                    },
                    deep: true,
                    immediate: true,
                },

                'available.records': {
                    handler() {
                        this.updateHorizontalScrollState();
                    },
                    deep: true,
                },

                columnWidths: {
                    handler() {
                        this.updateHorizontalScrollState();
                    },
                    deep: true,
                },
            },

            beforeUnmount() {
                window.removeEventListener('resize', this.updateHorizontalScrollState);
                window.removeEventListener('scroll', this.handleWindowScroll);

                this.detachResizeListeners();
            },

            methods: {
                ensureColumnWidths() {
                    const updated = { ...this.columnWidths };

                    this.available.columns.forEach((column) => {
                        if (! updated[column.index]) {
                            updated[column.index] = column.width ?? 180;
                        }
                    });

                    Object.keys(updated).forEach((key) => {
                        if (! this.available.columns.find(column => column.index === key)) {
                            delete updated[key];
                        }
                    });

                    this.columnWidths = updated;
                },

                updateHorizontalScrollState() {
                    this.$nextTick(() => {
                        const scrollable = this.$refs.scrollContainer;

                        if (! scrollable) {
                            this.showHorizontalScroll = false;
                            this.showFloatingScroll = false;

                            return;
                        }

                        this.scrollWidth = scrollable.scrollWidth;

                        this.showHorizontalScroll = scrollable.scrollWidth > scrollable.clientWidth;

                        if (this.showHorizontalScroll) {
                            this.syncHorizontalScroll('table');
                            this.updateFloatingScrollWrapper();
                            this.updateFloatingScrollVisibility();
                        } else {
                            this.showFloatingScroll = false;
                        }
                    });
                },

                startResize(event, column) {
                    event.stopPropagation();
                    event.preventDefault();

                    this.resizingColumn = column.index;
                    this.resizeStartX = event.clientX;
                    this.resizeStartWidth = this.columnWidths[column.index] ?? column.width ?? 180;

                    this.detachResizeListeners();

                    window.addEventListener('mousemove', this.onResizeMove);
                    window.addEventListener('mouseup', this.stopResize);
                },

                onResizeMove(event) {
                    if (! this.resizingColumn) {
                        return;
                    }

                    const delta = event.clientX - this.resizeStartX;

                    const nextWidth = Math.max(120, this.resizeStartWidth + delta);

                    this.columnWidths = {
                        ...this.columnWidths,
                        [this.resizingColumn]: nextWidth,
                    };
                },

                stopResize() {
                    this.resizingColumn = null;

                    this.detachResizeListeners();
                },

                detachResizeListeners() {
                    window.removeEventListener('mousemove', this.onResizeMove);
                    window.removeEventListener('mouseup', this.stopResize);
                },

                handleTableScroll() {
                    this.syncHorizontalScroll('table');
                },

                handleHorizontalScroll(event) {
                    this.syncHorizontalScroll('ghost', event.target.scrollLeft);
                },

                syncHorizontalScroll(source, overrideValue = null) {
                    if (! this.showHorizontalScroll || this.isSyncingHorizontalScroll) {
                        return;
                    }

                    const scrollable = this.$refs.scrollContainer;
                    const ghost = this.$refs.horizontalScroll;

                    if (! scrollable || ! ghost) {
                        return;
                    }

                    this.isSyncingHorizontalScroll = true;

                    if (source === 'table') {
                        ghost.scrollLeft = scrollable.scrollLeft;
                    } else {
                        scrollable.scrollLeft = overrideValue ?? ghost.scrollLeft;
                    }

                    this.$nextTick(() => {
                        this.isSyncingHorizontalScroll = false;
                    });
                },

                updateFloatingScrollWrapper() {
                    const scrollable = this.$refs.scrollContainer;

                    if (! scrollable) {
                        return;
                    }

                    const rect = scrollable.getBoundingClientRect();
                    const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
                    const horizontalPadding = 16;
                    const maxWidth = Math.max(
                        Math.min(rect.width, viewportWidth - horizontalPadding * 2),
                        0
                    );
                    const clampedLeft = Math.min(
                        Math.max(rect.left, horizontalPadding),
                        viewportWidth - maxWidth - horizontalPadding
                    );

                    this.floatingScrollWrapperStyles = {
                        left: `${clampedLeft}px`,
                        bottom: '24px',
                        width: `${maxWidth}px`,
                    };
                },

                updateFloatingScrollVisibility() {
                    if (! this.showHorizontalScroll) {
                        this.showFloatingScroll = false;

                        return;
                    }

                    const rootRect = this.$el?.getBoundingClientRect();

                    if (! rootRect) {
                        this.showFloatingScroll = false;

                        return;
                    }

                    const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                    const intersectsViewport = rootRect.top < viewportHeight && rootRect.bottom > 0;

                    this.showFloatingScroll = intersectsViewport;
                },

                handleWindowScroll() {
                    if (! this.showHorizontalScroll) {
                        return;
                    }

                    this.updateFloatingScrollVisibility();

                    if (this.showFloatingScroll) {
                        this.updateFloatingScrollWrapper();
                    }
                },

                /**
                 * Select all records in the datagrid.
                 *
                 * @returns {void}
                 */
                selectAll() {
                    this.$emit('selectAll');
                },

                /**
                 * Perform a sorting operation on the specified column.
                 *
                 * @param {object} column
                 * @returns {void}
                 */
                sort(column) {
                    this.$emit('sort', column);
                },

                /**
                 * Perform the specified action.
                 *
                 * @param {object} action
                 * @returns {void}
                 */
                performAction(action) {
                    const method = action.method.toLowerCase();

                    switch (method) {
                        case 'get':
                            window.location.href = action.url;

                            break;

                        case 'post':
                        case 'put':
                        case 'patch':
                        case 'delete':
                            this.$emitter.emit('open-confirm-modal', {
                                agree: () => {
                                    this.$axios[method](action.url)
                                        .then(response => {
                                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                            this.$emit('actionSuccess', response.data);
                                        })
                                        .catch((error) => {
                                            this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message });

                                            this.$emit('actionError', error.response.data);
                                        });
                                }
                            });

                            break;

                        default:
                            console.error('Method not supported.');

                            break;
                    }
                },
            },
        });
    </script>
@endpushOnce
