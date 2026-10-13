@if($errors->any())
    <div
        class="dashboard-card"
        style="
            margin-bottom:20px;
            border-inline-start:4px solid #dc2626;
        "
    >
        <strong>
            {{ __('admin_languages.validation_error') }}
        </strong>

        <ul style="margin-bottom:0;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="staff-form-grid">

    <div class="staff-form-group">
        <label
            for="code"
            class="staff-form-label"
        >
            {{ __('admin_languages.code') }}
        </label>

        <input
            id="code"
            type="text"
            name="code"
            maxlength="10"
            required
            class="staff-form-input"
            value="{{ old(
                'code',
                $language->code ?? ''
            ) }}"
            placeholder="en"
        >
    </div>


    <div class="staff-form-group">
        <label
            for="name"
            class="staff-form-label"
        >
            {{ __('admin_languages.name') }}
        </label>

        <input
            id="name"
            type="text"
            name="name"
            maxlength="100"
            required
            class="staff-form-input"
            value="{{ old(
                'name',
                $language->name ?? ''
            ) }}"
            placeholder="English"
        >
    </div>


    <div class="staff-form-group">
        <label
            for="native_name"
            class="staff-form-label"
        >
            {{ __('admin_languages.native_name') }}
        </label>

        <input
            id="native_name"
            type="text"
            name="native_name"
            maxlength="100"
            class="staff-form-input"
            value="{{ old(
                'native_name',
                $language->native_name ?? ''
            ) }}"
            placeholder="English / العربية"
        >
    </div>


    <div class="staff-form-group">
        <label
            for="direction"
            class="staff-form-label"
        >
            {{ __('admin_languages.direction') }}
        </label>

        <select
            id="direction"
            name="direction"
            class="staff-form-select"
            required
        >
            <option
                value="ltr"
                @selected(
                    old(
                        'direction',
                        $language->direction ?? 'ltr'
                    ) === 'ltr'
                )
            >
                {{ __('admin_languages.ltr') }}
            </option>

            <option
                value="rtl"
                @selected(
                    old(
                        'direction',
                        $language->direction ?? 'ltr'
                    ) === 'rtl'
                )
            >
                {{ __('admin_languages.rtl') }}
            </option>
        </select>
    </div>

</div>


<div
    class="roles-box"
    style="margin-top:20px;"
>
    <input
        type="hidden"
        name="is_active"
        value="0"
    >

    <label class="role-option">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked(
                old(
                    'is_active',
                    isset($language)
                        ? $language->is_active
                        : true
                )
            )
        >

        <span>
            <strong>
                {{ __('admin_languages.active') }}
            </strong>

            <small>
                {{ __('admin_languages.active_help') }}
            </small>
        </span>
    </label>
</div>


<div
    class="staff-form-actions"
    style="margin-top:22px;"
>
    <a
        href="{{ route('admin.languages.index') }}"
        class="staff-btn staff-btn-secondary"
        style="text-decoration:none;"
    >
        {{ __('admin_languages.cancel') }}
    </a>

    <button
        type="submit"
        class="staff-btn staff-btn-primary"
    >
        {{ __('admin_languages.save') }}
    </button>
</div>