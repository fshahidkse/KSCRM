<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.leads.create.title')
    </x-slot>

    {!! view_render_event('admin.leads.create.form.before') !!}

    <!-- Create Lead Form -->
    <x-admin::form :action="route('admin.leads.store')">
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs name="leads.create" />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.leads.create.title')
                    </div>
                </div>

                {!! view_render_event('admin.leads.create.save_button.before') !!}

                <div class="flex items-center gap-x-2.5">
                    <!-- Save button for person -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.leads.create.form_buttons.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.leads.create.save-btn')
                        </button>

                        {!! view_render_event('admin.leads.create.form_buttons.after') !!}
                    </div>
                </div>

                {!! view_render_event('admin.leads.create.save_button.after') !!}
            </div>

            @if (request('stage_id'))
                <input 
                    type="hidden" 
                    id="lead_pipeline_stage_id" 
                    name="lead_pipeline_stage_id" 
                    value="{{ request('stage_id') }}" 
                />
            @endif

            <!-- Lead Create Component -->
            <v-lead-create>
                <x-admin::shimmer.leads.datagrid />
            </v-lead-create>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.leads.create.form.after') !!}

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-lead-create-template"
        >
            <div class="box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                {!! view_render_event('admin.leads.edit.form_controls.before') !!}
                
                <div class="flex w-full gap-2 border-b border-gray-200 dark:border-gray-800">
                    <!-- Tabs -->
                    <template
                        v-for="tab in tabs"
                        :key="tab.id"
                    >
                        {!! view_render_event('admin.leads.create.tabs.before') !!}

                        <a
                            :href="'#' + tab.id"
                            :class="[
                                'inline-block px-3 py-2.5 border-b-2  text-sm font-medium ',
                                activeTab === tab.id
                                ? 'text-brandColor border-brandColor dark:brandColor dark:brandColor'
                                : 'text-gray-600 dark:text-gray-300  border-transparent hover:text-gray-800 hover:border-gray-400 dark:hover:border-gray-400  dark:hover:text-white'
                            ]"
                            @click="scrollToSection(tab.id)"
                            :text="tab.label"
                        >
                        </a>

                        {!! view_render_event('admin.leads.create.tabs.after') !!}
                    </template>
                </div>

                <div class="flex flex-col gap-4 px-4 py-2">
                    {!! view_render_event('admin.leads.create.details.before') !!}

                    <!-- Details section -->
                    <div 
                        class="flex flex-col gap-4" 
                        id="lead-details"
                    >
                        <div class="flex flex-col gap-1">
                            <p class="text-base font-semibold dark:text-white">
                                @lang('admin::app.leads.create.details')
                            </p>

                            <p class="text-gray-600 dark:text-white">
                                @lang('admin::app.leads.create.details-info')
                            </p>
                        </div>

                        <div class="w-1/2 max-md:w-full">
                            {!! view_render_event('admin.leads.create.details.attributes.before') !!}

                            <!-- Lead Details Title and Description -->
                            <div class="flex flex-col gap-4">
                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label
                                        for="title"
                                        class="required"
                                    >
                                        @lang('admin::app.leads.create.opportunity-name')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="title"
                                        id="title"
                                        value="{{ old('title') }}"
                                        rules="required"
                                    />

                                    <x-admin::form.control-group.error control-name="title" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label for="opportunity_number">
                                        @lang('admin::app.leads.create.opportunity-number')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="opportunity_number"
                                        id="opportunity_number"
                                        value="{{ old('opportunity_number') }}"
                                        
                                    />

                                    <x-admin::form.control-group.error control-name="opportunity_number" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label for="client_company">
                                        @lang('admin::app.leads.create.client-company')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="client_company"
                                        id="client_company"
                                        value="{{ old('client_company') }}"
                                        
                                    />

                                    <x-admin::form.control-group.error control-name="client_company" />
                                </x-admin::form.control-group>

                                <x-admin::form.control-group>
                                    <x-admin::form.control-group.label for="owner_company">
                                        @lang('admin::app.leads.create.owner-company')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="owner_company"
                                        id="owner_company"
                                        value="{{ old('owner_company') }}"
                                        
                                    />

                                    <x-admin::form.control-group.error control-name="owner_company" />
                                </x-admin::form.control-group>
                            </div>
                            <x-admin::attributes
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    ['code', 'NOTIN', [
                                        'title', 'opportunity_number', 'client_company', 'owner_company', 'lead_value', 'lead_type_id', 'lead_source_id', 'expected_close_date', 'user_id', 'lead_pipeline_id', 'lead_pipeline_stage_id',
                                        // Exclude all Lifecycle attribute codes:
                                        'quals_due_date',
                                        'proposal_due_date',
                                        'estimated_selection_date',
                                        'close_date',
                                        'estimated_start_date',
                                        'estimated_completion_date',
                                        // Exclude all Metrics attribute codes:
                                        'total_estimated_fee',
                                        'firm_estimated_fee',
                                        'win_probability',
                                        // Exclude all Address attribute codes:
                                        'project_address_1',
                                        'project_city',
                                        'project_state',
                                        'project_postal_code'
                                    ]],
                                    'entity_type' => 'leads',
                                    'quick_add'   => 1
                                ])"
                                :custom-validations="[
                                    'expected_close_date' => [
                                        'date_format:yyyy-MM-dd',
                                        'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                    ],
                                ]"
                            />

                            <!-- Lead Details Other input fields -->
                            <div class="flex gap-4 max-sm:flex-wrap">
                                <div class="w-full">
                                    <x-admin::attributes
                                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                            ['code', 'IN', ['lead_value', 'lead_type_id', 'lead_source_id']],
                                            'entity_type' => 'leads',
                                            'quick_add'   => 1
                                        ])"
                                        :custom-validations="[
                                            'expected_close_date' => [
                                                'date_format:yyyy-MM-dd',
                                                'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                            ],
                                        ]"
                                    />
                                </div>
                                    
                                <div class="w-full">
                                    <x-admin::attributes
                                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                            ['code', 'IN', ['expected_close_date', 'user_id']],
                                            'entity_type' => 'leads',
                                            'quick_add'   => 1
                                        ])"
                                        :custom-validations="[
                                            'expected_close_date' => [
                                                'date_format:yyyy-MM-dd',
                                                'after:' .  \Carbon\Carbon::yesterday()->format('Y-m-d')
                                            ],
                                        ]"
                                    />
                                </div>
                            </div>

                            {!! view_render_event('admin.leads.create.details.attributes.after') !!}
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.create.details.after') !!}

                    <!-- Address Section -->
                    <div id="address" class="mt-8">
                        <h4 class="text-lg font-semibold mb-4">Address</h4>
                        <x-admin::attributes
                            :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                ['code', 'IN', ['project_address_1', 'project_city', 'project_state', 'project_postal_code']],
                                'entity_type' => 'leads',
                                'quick_add'   => 1
                            ])"
                        />
                    </div>

                    <!-- Metrics Section -->
                    <div id="metrics" class="mt-8">
                        <h4 class="text-lg font-semibold mb-4">Metrics</h4>
                        @php
                            $metricAttributes = app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                ['code', 'IN', ['total_estimated_fee', 'firm_estimated_fee', 'win_probability']],
                                'entity_type' => 'leads',
                                'quick_add'   => 1,
                            ]);
                        @endphp

                        <div class="flex flex-wrap gap-4 md:flex-nowrap">
                            @foreach ($metricAttributes as $attribute)
                                @php
                                    $validations = [];

                                    if ($attribute->is_required) {
                                        $validations[] = 'required';
                                    }

                                    if ($attribute->validation) {
                                        $validations[] = $attribute->validation === 'decimal'
                                            ? 'decimal'
                                            : $attribute->validation;
                                    }

                                    if ($attribute->type === 'price' && ! in_array('decimal', $validations)) {
                                        $validations[] = 'decimal';
                                    }

                                    $validationString = implode('|', array_filter($validations));
                                @endphp

                                <x-admin::form.control-group class="flex-1 min-w-[12rem]">
                                    <x-admin::form.control-group.label
                                        for="{{ $attribute->code }}"
                                        :class="$attribute->is_required ? 'required' : ''"
                                    >
                                        {{ $attribute->name }}
                                    </x-admin::form.control-group.label>

                                    <x-admin::attributes.edit.index
                                        :attribute="$attribute"
                                        :value="old($attribute->code)"
                                        :validations="$validationString"
                                    />

                                    <x-admin::form.control-group.error :control-name="$attribute->code" />
                                </x-admin::form.control-group>
                            @endforeach
                        </div>
                    </div>

                    <!-- Lifecycle Section -->
                    <div id="lifecycle" class="mt-8">
                        <h4 class="text-lg font-semibold mb-4">Lifecycle</h4>
                        @php
                            $lifecycleAttributes = app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                ['code', 'IN', [
                                    'quals_due_date',
                                    'proposal_due_date',
                                    'estimated_selection_date',
                                    'close_date',
                                    'estimated_start_date',
                                    'estimated_completion_date'
                                ]],
                                'entity_type' => 'leads',
                                'quick_add'   => 1,
                            ]);
                        @endphp

                        <div class="flex flex-wrap gap-4 md:flex-nowrap">
                            @foreach ($lifecycleAttributes as $attribute)
                                @php
                                    $validations = [];

                                    if ($attribute->is_required) {
                                        $validations[] = 'required';
                                    }

                                    if ($attribute->validation) {
                                        $validations[] = $attribute->validation === 'decimal'
                                            ? 'decimal'
                                            : $attribute->validation;
                                    }

                                    if ($attribute->type === 'price' && ! in_array('decimal', $validations)) {
                                        $validations[] = 'decimal';
                                    }

                                    $validationString = implode('|', array_filter($validations));
                                @endphp

                                <x-admin::form.control-group class="flex-1 min-w-[12rem]">
                                    <x-admin::form.control-group.label
                                        for="{{ $attribute->code }}"
                                        :class="$attribute->is_required ? 'required' : ''"
                                    >
                                        {{ $attribute->name }}
                                    </x-admin::form.control-group.label>

                                    <x-admin::attributes.edit.index
                                        :attribute="$attribute"
                                        :value="old($attribute->code)"
                                        :validations="$validationString"
                                    />

                                    <x-admin::form.control-group.error :control-name="$attribute->code" />
                                </x-admin::form.control-group>
                            @endforeach
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.create.contact_person.before') !!}
                    
                    <!-- Contact Person -->
                    <div 
                        class="flex flex-col gap-4" 
                        id="contact-person"
                    >
                        <div class="flex flex-col gap-1">
                            <p class="text-base font-semibold dark:text-white">
                                @lang('admin::app.leads.create.contact-person')
                            </p>

                            <p class="text-gray-600 dark:text-white">
                                @lang('admin::app.leads.create.contact-info')
                            </p>
                        </div>

                        <div class="w-1/2 max-md:w-full">
                            <!-- Contact Person Component -->
                            @include('admin::leads.common.contact')
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.create.contact_person.after') !!}

                    <!-- Product Section -->
                    <!-- <div 
                        class="flex flex-col gap-4" 
                        id="products"
                    > -->
                        <!-- <div class="flex flex-col gap-1">
                            <p class="text-base font-semibold dark:text-white">
                                @lang('admin::app.leads.create.products')
                            </p>

                            <p class="text-gray-600 dark:text-white">
                                @lang('admin::app.leads.create.products-info')
                            </p>
                        </div> -->

                        <!-- <div>
                            <!-- Product Component -->
                            <!-- @include('admin::leads.common.products') -->
                        <!-- </div>  -->
                    <!-- </div> --> 
                </div>
                
                {!! view_render_event('admin.leads.form_controls.after') !!}
            </div>
        </script>

        <script type="module">
            app.component('v-lead-create', {
                template: '#v-lead-create-template',

                data() {
                    return {
                        activeTab: 'lead-details',

                        tabs: [
                            { id: 'lead-details', label: '@lang('admin::app.leads.create.details')' },
                            { id: 'address', label: 'Address' },
                            { id: 'metrics', label: 'Metrics' },
                            { id: 'lifecycle', label: 'Lifecycle' },
                            { id: 'contact-person', label: '@lang('admin::app.leads.create.contact-person')' },
                            // { id: 'products', label: '@lang('admin::app.leads.create.products')' }
                        ],
                    };
                },

                methods: {
                    /**
                     * Scroll to the section.
                     * 
                     * @param {String} tabId
                     * 
                     * @returns {void}
                     */
                    scrollToSection(tabId) {
                        const section = document.getElementById(tabId);

                        if (section) {
                            section.scrollIntoView({ behavior: 'smooth' });
                        }
                    },
                },
            });
        </script>
    @endPushOnce

    @pushOnce('styles')
        <style>
            html {
                scroll-behavior: smooth;
            }
        </style>
    @endPushOnce    
</x-admin::layouts>
