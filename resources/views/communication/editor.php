<div class="form-wide rich-editor mailbox-editor" data-rich-editor>
    <div class="editor-title">
        <span><?= e($label ?? 'Message') ?></span>
        <button type="button" data-editor-toggle>HTML Code</button>
    </div>
    <div class="editor-toolbar">
        <button type="button" data-command="bold"><strong>B</strong></button>
        <button type="button" data-command="italic"><em>I</em></button>
        <button type="button" data-command="underline"><u>U</u></button>
        <select data-command="fontName">
            <option value="">Font</option>
            <option value="Arial">Arial</option>
            <option value="Georgia">Georgia</option>
            <option value="Tahoma">Tahoma</option>
            <option value="Times New Roman">Times New Roman</option>
            <option value="Verdana">Verdana</option>
        </select>
        <select data-command="fontSize">
            <option value="">Size</option>
            <option value="2">Small</option>
            <option value="3">Normal</option>
            <option value="4">Large</option>
            <option value="5">Heading</option>
        </select>
        <select data-command="formatBlock">
            <option value="">Style</option>
            <option value="h2">Heading</option>
            <option value="h3">Subheading</option>
            <option value="p">Paragraph</option>
        </select>
        <input type="color" value="#17394a" data-command="foreColor" title="Text color">
        <button type="button" data-create-link>Link</button>
    </div>
    <div class="editor-surface" contenteditable="true" data-editor-surface><?= $defaultMessage ?? '' ?></div>
    <textarea name="<?= e($fieldName ?? 'message') ?>" rows="6" data-editor-source <?= !empty($required) ? 'required' : '' ?>><?= e($defaultMessage ?? '') ?></textarea>
</div>
