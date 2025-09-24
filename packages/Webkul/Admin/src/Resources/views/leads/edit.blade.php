<x-admin::layouts>
    <!-- Page Title -->
    <x-slot:title>
        @lang('admin::app.leads.edit.title')
    </x-slot>

    {!! view_render_event('admin.leads.edit.form_controls.before', ['lead' => $lead]) !!}

    <!-- Edit Lead Form -->
    <x-admin::form         
        :action="route('admin.leads.update', $lead->id)"
        method="PUT"
    >
        <div class="flex flex-col gap-4">
            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <div class="flex flex-col gap-2">
                    <x-admin::breadcrumbs 
                        name="leads.edit" 
                        :entity="$lead"
                    />

                    <div class="text-xl font-bold dark:text-white">
                        @lang('admin::app.leads.edit.title')
                    </div>
                </div>

                <div class="flex items-center gap-x-2.5">
                    {!! view_render_event('admin.leads.edit.save_button.before', ['lead' => $lead]) !!}

                    <!-- Save button for Editing Lead -->
                    <div class="flex items-center gap-x-2.5">
                        {!! view_render_event('admin.leads.edit.form_buttons.before') !!}

                        <button
                            type="submit"
                            class="primary-button"
                        >
                            @lang('admin::app.leads.edit.save-btn')
                        </button>

                        {!! view_render_event('admin.leads.edit.form_buttons.after') !!}
                    </div>

                    {!! view_render_event('admin.leads.edit.save_button.after', ['lead' => $lead]) !!}
                </div>
            </div>

            <input type="hidden" id="lead_pipeline_stage_id" name="lead_pipeline_stage_id" value="{{ $lead->lead_pipeline_stage_id }}" />

            <!-- Lead Edit Component -->
            <v-lead-edit :lead="{{ json_encode($lead) }}">
                <x-admin::shimmer.leads.datagrid />
            </v-lead-edit>
        </div>
    </x-admin::form>

    {!! view_render_event('admin.leads.edit.form_controls.after', ['lead' => $lead]) !!}

    @pushOnce('scripts')
        <script 
            type="text/x-template"
            id="v-lead-edit-template"
        >
            <div class="box-shadow flex flex-col gap-4 rounded-lg border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex gap-2 border-b border-gray-200 dark:border-gray-800">
                    <!-- Tabs -->
                    <template v-for="tab in tabs" :key="tab.id">
                        {!! view_render_event('admin.leads.edit.tabs.before', ['lead' => $lead]) !!}

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
                        ></a>

                        {!! view_render_event('admin.leads.edit.tabs.after', ['lead' => $lead]) !!}
                    </template>
                </div>

                <div class="flex flex-col gap-4 px-4 py-2">
                    {!! view_render_event('admin.leads.edit.lead_details.before', ['lead' => $lead]) !!}

                    <!-- Details section -->
                    <div 
                        class="flex flex-col gap-4" 
                        id="lead-details"
                    >
                        <div class="flex flex-col gap-1">
                            <p class="text-base font-semibold dark:text-white">
                                @lang('admin::app.leads.edit.details')
                            </p>

                            <p class="text-gray-600 dark:text-white">
                                @lang('admin::app.leads.edit.details-info')
                            </p>
                        </div>

                        <div class="w-full">
                            {!! view_render_event('admin.leads.edit.lead_details.attributes.before', ['lead' => $lead]) !!}

                            <!-- Lead Details Title and Description -->
                            <div class="flex flex-col gap-4">
                                <div class="flex flex-wrap gap-4 md:flex-nowrap">
                                    <x-admin::form.control-group class="flex-1 min-w-[12rem]">
                                        <x-admin::form.control-group.label
                                            for="title"
                                            class="required"
                                        >
                                            @lang('admin::app.leads.edit.opportunity-name')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="title"
                                            id="title"
                                            value="{{ old('title', $lead->title) }}"
                                            rules="required"
                                        />

                                        <x-admin::form.control-group.error control-name="title" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group class="flex-1 min-w-[12rem]">
                                        <x-admin::form.control-group.label for="opportunity_number" class="required">
                                            @lang('admin::app.leads.edit.opportunity-number')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="opportunity_number"
                                            id="opportunity_number"
                                            value="{{ old('opportunity_number', $lead->opportunity_number) }}"
                                            rules="required"
                                        />

                                        <x-admin::form.control-group.error control-name="opportunity_number" />
                                    </x-admin::form.control-group>
                                </div>

                                <div class="flex flex-wrap gap-4 md:flex-nowrap">
                                    <x-admin::form.control-group class="flex-1 min-w-[12rem]">
                                        <x-admin::form.control-group.label for="client_company">
                                            @lang('admin::app.leads.edit.client-company')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="client_company"
                                            id="client_company"
                                            value="{{ old('client_company', $lead->client_company) }}"
                                        />

                                        <x-admin::form.control-group.error control-name="client_company" />
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group class="flex-1 min-w-[12rem]">
                                        <x-admin::form.control-group.label for="owner_company">
                                            @lang('admin::app.leads.edit.owner-company')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="owner_company"
                                            id="owner_company"
                                            value="{{ old('owner_company', $lead->owner_company) }}"
                                        />

                                        <x-admin::form.control-group.error control-name="owner_company" />
                                    </x-admin::form.control-group>
                                </div>
                            </div>

                            <x-admin::attributes
                                :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                    ['code', 'NOTIN', [
                                        'title',
                                        'opportunity_number',
                                        'client_company',
                                        'owner_company',
                                        'lead_value',
                                        'lead_type_id',
                                        'lead_source_id',
                                        'expected_close_date',
                                        'user_id',
                                        'lead_pipeline_id',
                                        'lead_pipeline_stage_id',
                                        'quals_due_date',
                                        'proposal_due_date',
                                        'estimated_selection_date',
                                        'close_date',
                                        'estimated_start_date',
                                        'estimated_completion_date',
                                        'total_estimated_fee',
                                        'firm_estimated_fee',
                                        'win_probability',
                                        'project_address_1',
                                        'project_city',
                                        'project_state',
                                        'project_postal_code',
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
                                :entity="$lead"
                            />

                            <!-- Lead Details Other input fields -->
                            <div class="flex gap-4 max-sm:flex-wrap">
                                <div class="w-full">
                                    <x-admin::attributes
                                        :custom-attributes="app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                            ['code', 'IN', ['lead_value']],
                                            'entity_type' => 'leads',
                                            'quick_add'   => 1
                                        ])"
                                        :entity="$lead"
                                    />
                                </div>
                            </div>

                            {!! view_render_event('admin.leads.edit.lead_details.attributes.after', ['lead' => $lead]) !!}
                        </div>

                    <!-- Address Section -->
                    <div id="address" class="mt-8">
                        <h4 class="text-lg font-semibold mb-4">Address</h4>
                        @php
                            $addressAttributes = app('Webkul\Attribute\Repositories\AttributeRepository')->findWhere([
                                ['code', 'IN', ['project_address_1', 'project_city', 'project_state', 'project_postal_code']],
                                'entity_type' => 'leads',
                                'quick_add'   => 1,
                            ])->keyBy('code');

                            $addressOrder = [
                                'project_address_1',
                                'project_city',
                                'project_postal_code',
                                'project_state',
                            ];
                        @endphp

                        <div class="flex flex-wrap gap-4">
                            @foreach (['project_address_1', 'project_city'] as $code)
                                @php
                                    $attribute = $addressAttributes[$code] ?? null;

                                    if (! $attribute) {
                                        continue;
                                    }

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
                                        :value="old($attribute->code, $lead->{$attribute->code} ?? null)"
                                        :validations="$validationString"
                                    />

                                    <x-admin::form.control-group.error :control-name="$attribute->code" />
                                </x-admin::form.control-group>
                            @endforeach
                        </div>

                        <div class="flex flex-wrap gap-4">
                            @foreach (['project_postal_code', 'project_state'] as $code)
                                @php
                                    $attribute = $addressAttributes[$code] ?? null;

                                    if (! $attribute) {
                                        continue;
                                    }

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
                                        :value="old($attribute->code, $lead->{$attribute->code} ?? null)"
                                        :validations="$validationString"
                                    />

                                    <x-admin::form.control-group.error :control-name="$attribute->code" />
                                </x-admin::form.control-group>
                            @endforeach
                        </div>
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
                                        :value="old($attribute->code, $lead->{$attribute->code} ?? null)"
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
                                        :value="old($attribute->code, $lead->{$attribute->code} ?? null)"
                                        :validations="$validationString"
                                    />

                                    <x-admin::form.control-group.error :control-name="$attribute->code" />
                                </x-admin::form.control-group>
                            @endforeach
                        </div>
                    </div>
                    </div>

                    {!! view_render_event('admin.leads.edit.lead_details.after', ['lead' => $lead]) !!}

                    {!! view_render_event('admin.leads.edit.contact_person.before', ['lead' => $lead]) !!}

                    <!-- Contact Person -->
                    <div 
                        class="flex flex-col gap-4" 
                        id="contact-person"
                    >
                        <div class="flex flex-col gap-1">
                            <p class="text-base font-semibold dark:text-white">
                                @lang('admin::app.leads.edit.contact-person')
                            </p>

                            <p class="text-gray-600 dark:text-white">
                                @lang('admin::app.leads.edit.contact-info')
                            </p>
                        </div>

                        <div class="w-1/2 max-md:w-full">
                            <!-- Contact Person Component -->
                            @include('admin::leads.common.contact')
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.edit.contact_person.after', ['lead' => $lead]) !!}

                    {!! view_render_event('admin.leads.edit.contact_person.products.before', ['lead' => $lead]) !!}

                    <!-- Product Section -->
                    <div 
                        class="flex flex-col gap-4" 
                        id="products"
                    >
                        <div class="flex flex-col gap-1">
                            <p class="text-base font-semibold dark:text-white">
                                @lang('admin::app.leads.edit.products')
                            </p>

                            <p class="text-gray-600 dark:text-white">
                                @lang('admin::app.leads.edit.products-info')
                            </p>
                        </div>

                        <div>
                            <!-- Product Component -->
                            @include('admin::leads.common.products')
                        </div>
                    </div>

                    {!! view_render_event('admin.leads.edit.contact_person.products.after', ['lead' => $lead]) !!}
                </div>
                
                {!! view_render_event('admin.leads.form_controls.after') !!}
            </div>
        </script>

        <script type="module">
            app.component('v-lead-edit', {
                template: '#v-lead-edit-template',

                data() {
                    return {
                        activeTab: 'lead-details',
                        
                        lead:  @json($lead),  

                        person:  @json($lead->person),  

                        products: @json($lead->products),

                        tabs: [
                            { id: 'lead-details', label: '@lang('admin::app.leads.edit.details')' },
                            { id: 'address', label: 'Address' },
                            { id: 'metrics', label: 'Metrics' },
                            { id: 'lifecycle', label: 'Lifecycle' },
                            { id: 'contact-person', label: '@lang('admin::app.leads.edit.contact-person')' },
                            { id: 'products', label: '@lang('admin::app.leads.edit.products')' }
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

