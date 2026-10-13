@if($errors->any())
    <div
        class="dashboard-card"
        style="
            margin-bottom:20px;
            border-inline-start:4px solid #dc2626;
        "
    >
        <strong>
            {{ __('admin_skills.validation_error') }}
        </strong>

        <ul style="margin-bottom:0;">
            @foreach($errors->all() as $error)
                <li>
                    {{ $error }}
                </li>
            @endforeach
        </ul>
    </div>
@endif


<div class="staff-form-grid">

    <div class="staff-form-group">
        <label
            for="name"
            class="staff-form-label"
        >
            {{ __('admin_skills.name') }}
        </label>

        <input
            id="name"
            type="text"
            name="name"
            value="{{ old(
                'name',
                $skill->name ?? ''
            ) }}"
            class="staff-form-input"
            maxlength="100"
            required
        >
    </div>


    <div class="staff-form-group">
        <label
            for="category"
            class="staff-form-label"
        >
            {{ __('admin_skills.category') }}
        </label>

        <input
            id="category"
            type="text"
            name="category"
            value="{{ old(
                'category',
                $skill->category ?? ''
            ) }}"
            class="staff-form-input"
            maxlength="50"
            placeholder="{{ __('admin_skills.category_placeholder') }}"
        >
    </div>

</div>


<div
    class="staff-form-group"
    style="margin-top:20px;"
>
    <label
        for="aliases_text"
        class="staff-form-label"
    >
        {{ __('admin_skills.aliases') }}
    </label>

    <textarea
        id="aliases_text"
        name="aliases_text"
        class="staff-form-input"
        rows="10"
        style="
            resize:vertical;
            min-height:220px;
        "
        placeholder="{{ __('admin_skills.aliases_placeholder') }}"
    >{{ old(
        'aliases_text',
        $aliasesText ?? ''
    ) }}</textarea>

    <div
        style="
            margin-top:8px;
            font-size:13px;
            opacity:.7;
            line-height:1.7;
        "
    >
        {{ __('admin_skills.aliases_help') }}
    </div>
</div>


<div
    class="staff-form-actions"
    style="margin-top:22px;"
>
    <a
        href="{{ route('admin.skills.index') }}"
        class="staff-btn staff-btn-secondary"
        style="text-decoration:none;"
    >
        {{ __('admin_skills.cancel') }}
    </a>

    <button
        type="submit"
        class="staff-btn staff-btn-primary"
    >
        {{ __('admin_skills.save') }}
    </button>
</div>