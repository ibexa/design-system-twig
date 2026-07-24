import { BaseChoiceInput } from '../../partials';

const TOGGLE_RADIO_INPUTS_COUNT = 2;

export class ToggleButtonInput extends BaseChoiceInput {
    private labels: { on: string; off: string };
    private inputType: 'checkbox' | 'radio';
    private offInputElement?: HTMLInputElement;
    private onInputElement: HTMLInputElement;
    private widgetNode: HTMLDivElement;
    private toggleLabelNode: HTMLLabelElement;

    static EVENTS = {
        ...BaseChoiceInput.EVENTS,
        CHANGE: 'ids:toggle-button-input:change',
    };

    constructor(container: HTMLDivElement) {
        const inputElements = [...container.querySelectorAll<HTMLInputElement>('.ids-toggle__source input')];
        const checkboxInput = inputElements.find((inputElement) => inputElement.type === 'checkbox');
        const radioInputs = inputElements.filter((inputElement) => inputElement.type === 'radio');

        let inputElement: HTMLInputElement;

        if (checkboxInput && radioInputs.length) {
            throw new Error('ToggleButtonInput: Mixed input types are not supported in the container.');
        }

        if (checkboxInput) {
            inputElement = checkboxInput;
            super(inputElement);

            this.inputType = 'checkbox';
            this.onInputElement = inputElement;
        } else if (radioInputs.length) {
            const resolvedRadioInputs = ToggleButtonInput.resolveRadioInputs(radioInputs);

            inputElement = resolvedRadioInputs.onInputElement;
            super(inputElement);

            this.inputType = 'radio';
            this.onInputElement = resolvedRadioInputs.onInputElement;
            this.offInputElement = resolvedRadioInputs.offInputElement;
        } else {
            throw new Error('ToggleButtonInput: Input element is missing in the container.');
        }

        this._container = container;

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

    protected static resolveRadioInputs(radioInputs: HTMLInputElement[]): {
        onInputElement: HTMLInputElement;
        offInputElement: HTMLInputElement;
    } {
        if (radioInputs.length !== TOGGLE_RADIO_INPUTS_COUNT) {
            throw new Error('ToggleButtonInput: Toggle radio mode requires exactly two radio inputs.');
        }

        const [firstRadioInput, secondRadioInput] = radioInputs;

        if (!firstRadioInput.name || firstRadioInput.name !== secondRadioInput.name) {
            throw new Error('ToggleButtonInput: Toggle radio inputs must share the same name.');
        }

        const radioInputByValue = new Map(radioInputs.map((radioInput) => [radioInput.value, radioInput]));
        const onInputElement = radioInputByValue.get('1') ?? firstRadioInput;
        const offInputElement = radioInputByValue.get('0') ?? radioInputs.find((radioInput) => radioInput !== onInputElement);

        if (!offInputElement) {
            throw new Error('ToggleButtonInput: Could not resolve the off radio input.');
        }

        return { offInputElement, onInputElement };
    }

    protected getInputElements(): HTMLInputElement[] {
        return this.inputType === 'radio' && this.offInputElement ? [this.onInputElement, this.offInputElement] : [this.onInputElement];
    }

    protected isChecked(): boolean {
        return this.onInputElement.checked;
    }

    protected getInputElementToFocus(): HTMLInputElement {
        if (this.inputType === 'radio') {
            return this.getCheckedInputElement() ?? this.onInputElement;
        }

        return this.onInputElement;
    }

    protected getCheckedInputElement(): HTMLInputElement | null {
        return this.getInputElements().find((inputElement) => inputElement.checked) ?? null;
    }

    protected syncVisualState(): void {
        const isChecked = this.isChecked();

        this.updateLabel();
        this._container.classList.toggle('ids-toggle--checked', isChecked);
    }

    protected updateLabel(): void {
        const isChecked = this.isChecked();

        this.toggleLabelNode.textContent = isChecked ? this.labels.on : this.labels.off;
    }

    protected initWidgets(): void {
        this.widgetNode.addEventListener('click', () => {
            const inputElementToFocus = this.getInputElementToFocus();

            if (inputElementToFocus.disabled) {
                return;
            }

            inputElementToFocus.focus();

            if (this.inputType === 'radio') {
                const inputElementToCheck = this.isChecked() ? this.offInputElement : this.onInputElement;

                if (!inputElementToCheck || inputElementToCheck.checked) {
                    return;
                }

                inputElementToCheck.checked = true;
                inputElementToCheck.dispatchEvent(new Event('change', { bubbles: true }));

                return;
            }

            this.onInputElement.checked = !this.onInputElement.checked;
            this.onInputElement.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    protected initInputEvents(): void {
        this.getInputElements().forEach((inputElement) => {
            inputElement.addEventListener('focus', () => {
                this._container.classList.add('ids-toggle--focused');
            });

            inputElement.addEventListener('blur', () => {
                this._container.classList.remove('ids-toggle--focused');
            });

            inputElement.addEventListener('change', () => {
                const changeEvent = new CustomEvent(ToggleButtonInput.EVENTS.CHANGE, {
                    bubbles: true,
                    detail: this.isChecked(),
                });

                this.syncVisualState();
                this._container.dispatchEvent(changeEvent);
            });
        });
    }

    public init() {
        super.init();

        this.syncVisualState();
        this.initInputEvents();
        this.initWidgets();
    }
}
