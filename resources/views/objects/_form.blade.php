@php
    $object = $object ?? null;
@endphp

<div class="space-y-5">
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700">Titlu <span class="text-red-500">*</span></label>
        <input id="title" type="text" name="title" value="{{ old('title', $object?->title) }}" required
            placeholder="ex. Scară telescopică"
            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700">Categorie <span class="text-red-500">*</span></label>
            <select id="category_id" name="category_id" required
                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">Alege o categorie</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $object?->category_id) == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="condition" class="block text-sm font-medium text-gray-700">Stare <span class="text-red-500">*</span></label>
            <select id="condition" name="condition" required
                class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
                @foreach ($conditions as $value => $label)
                    <option value="{{ $value }}" @selected(old('condition', $object?->condition?->value) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700">Descriere</label>
        <textarea id="description" name="description" rows="4"
            placeholder="Descrie obiectul și cum poate fi folosit..."
            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('description', $object?->description) }}</textarea>
    </div>

    <div>
        <label for="max_borrow_days" class="block text-sm font-medium text-gray-700">Perioadă maximă de împrumut (zile) <span class="text-red-500">*</span></label>
        <input id="max_borrow_days" type="number" name="max_borrow_days" value="{{ old('max_borrow_days', $object?->max_borrow_days ?? 7) }}" required min="1" max="365"
            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">
    </div>

    <div class="space-y-3 rounded-2xl border border-gray-100 bg-gray-50 p-4">
        <label class="flex items-start gap-3">
            <input type="checkbox" name="requires_personal_handover" value="1" @checked(old('requires_personal_handover', $object?->requires_personal_handover))
                class="mt-0.5 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm text-gray-700">Necesită predare personală</span>
        </label>
        <label class="flex items-start gap-3">
            <input type="checkbox" name="can_leave_at_door" value="1" @checked(old('can_leave_at_door', $object?->can_leave_at_door))
                class="mt-0.5 h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm text-gray-700">Poate fi lăsat la ușa apartamentului</span>
        </label>
    </div>

    <div>
        <label for="special_conditions" class="block text-sm font-medium text-gray-700">Condiții speciale</label>
        <textarea id="special_conditions" name="special_conditions" rows="2"
            placeholder="ex. Se împrumută doar vecinilor din aceeași scară."
            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('special_conditions', $object?->special_conditions) }}</textarea>
    </div>

    <div>
        <label for="usage_instructions" class="block text-sm font-medium text-gray-700">Instrucțiuni de utilizare</label>
        <textarea id="usage_instructions" name="usage_instructions" rows="2"
            placeholder="ex. Folosiți doar cu prelungitor inclus."
            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('usage_instructions', $object?->usage_instructions) }}</textarea>
    </div>

    <div>
        <label for="images" class="block text-sm font-medium text-gray-700">Fotografii</label>
        <input id="images" type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple
            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-xl file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
        <p class="mt-1 text-xs text-gray-400">Maxim 6 fotografii, până la 5 MB fiecare (JPEG, PNG, WebP).</p>
    </div>
</div>
