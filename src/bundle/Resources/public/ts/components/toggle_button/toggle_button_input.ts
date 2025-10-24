import { BaseChoiceInput } from '../../partials';

export class ToggleButtonInput extends BaseChoiceInput {
    private labels: { on: string; off: string };
    private widgetNode: HTMLDivElement;
    private toggleLabelNode: HTMLLabelElement;

    static EVENTS = {
        ...BaseChoiceInput.EVENTS,
        CHANGE: 'ids:toggle-button-input:change',
    };

    constructor(container: HTMLDivElement) {
        super(container);

        const widgetNode = this._container.querySelector<HTMLDivElement>('.ids-toggle__widget');
        const toggleLabelNode = this._container.querySelector<HTMLLabelElement>('.ids-toggle__label');

        if (!widgetNode || !toggleLabelNode) {
            throw new Error('ToggleButtonInput: Required elements are missing in the container.');
        }

        const labelOn = toggleLabelNode.getAttribute('data-ids-label-on');
        const labelOff = toggleLabelNode.getAttribute('data-ids-label-off');

        if (!labelOn || !labelOff) {
            throw new Error('ToggleButtonInput: Toggle labels are missing in label attributes.');
        }

        this.labels = { off: labelOff, on: labelOn };
        this.widgetNode = widgetNode;
        this.toggleLabelNode = toggleLabelNode;
    }

    protected updateLabel(): void {
        const isChecked = this._inputElement.checked;

        if (isChecked) {
            this.toggleLabelNode.textContent = this.labels.on;
        } else {
            this.toggleLabelNode.textContent = this.labels.off;
        }
    }

    protected initWidgets(): void {
        this.widgetNode.addEventListener('click', () => {
            this._inputElement.focus();
            this._inputElement.checked = !this._inputElement.checked;
            this._inputElement.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    protected initInputEvents(): void {
        this._inputElement.addEventListener('focus', () => {
            this._container.classList.add('ids-toggle--focused');
        });

        this._inputElement.addEventListener('blur', () => {
            this._container.classList.remove('ids-toggle--focused');
        });

        this._inputElement.addEventListener('change', () => {
            const changeEvent = new CustomEvent(ToggleButtonInput.EVENTS.CHANGE, {
                bubbles: true,
                detail: this._inputElement.checked,
            });

            this.updateLabel();
            this._container.classList.toggle('ids-toggle--checked', this._inputElement.checked);
            this._container.dispatchEvent(changeEvent);
        });
    }

    public init() {
        super.init();

        this.initInputEvents();
        this.initWidgets();
    }
}
