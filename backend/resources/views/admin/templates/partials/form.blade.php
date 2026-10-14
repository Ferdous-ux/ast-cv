<div class="staff-form-grid">
    <div class="staff-form-group">
        <label class="staff-form-label">
            {{ __('templates.name') }}
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $template?->name) }}"
            class="staff-form-input"
            required
        >

        @error('name')
            <div>{{ $message }}</div>
        @enderror
    </div>

    <div class="staff-form-group">
        <label class="staff-form-label">
            {{ __('templates.key') }}
        </label>

        <input
            type="text"
            name="key"
            value="{{ old('key', $template?->key) }}"
            class="staff-form-input"
            placeholder="default"
            required
        >

        @error('key')
            <div>{{ $message }}</div>
        @enderror
    </div>

    <div class="staff-form-group">
        <label class="staff-form-label">
            {{ __('templates.sort_order') }}
        </label>

        <input
            type="number"
            min="0"
            max="9999"
            name="sort_order"
            value="{{ old('sort_order', $template?->sort_order ?? 0) }}"
            class="staff-form-input"
            required
        >

        @error('sort_order')
            <div>{{ $message }}</div>
        @enderror
    </div>

    <div class="staff-form-group">
        <label class="staff-form-label">
            {{ __('templates.thumbnail') }}
        </label>

        <input
            type="text"
            name="thumbnail"
            value="{{ old('thumbnail', $template?->thumbnail) }}"
            class="staff-form-input"
            placeholder="images/templates/default.png"
        >

        @error('thumbnail')
            <div>{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="staff-form-group" style="margin-top:18px;">
    <label class="staff-form-label">
        {{ __('templates.description_field') }}
    </label>

    <textarea
        name="description"
        rows="5"
        class="staff-form-input"
    >{{ old('description', $template?->description) }}</textarea>

    @error('description')
        <div>{{ $message }}</div>
    @enderror
</div>

<div class="roles-box" style="margin-top:18px;">
    <label class="role-option">
        <input
            type="checkbox"
            name="is_active"
            value="1"
            @checked(old('is_active', $template?->is_active ?? true))
        >

        <span>{{ __('templates.active') }}</span>
    </label>
</div>
