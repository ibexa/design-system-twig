import { Base } from '../../partials';

export interface AltRadioInputOptions {
    onTileClick?: (event: MouseEvent, inputId: string) => void;
}

export class AltRadioInput extends Base {
    private inputElement: HTMLInputElement;
    private tileElement: HTMLDivElement;
    private onTileClick?: (event: MouseEvent, inputId: string) => void;

    private isChecked = false;
    private isFocused = false;

    constructor(container: HTMLDivElement, { onTileClick }: AltRadioInputOptions = {}) {
        super(container);

        const inputElement = this._container.querySelector<HTMLInputElement>('.ids-alt-radio__source .ids-input');
        const tileElement = this._container.querySelector<HTMLDivElement>('.ids-alt-radio__tile');

        if (!inputElement || !tileElement) {
            throw new Error('AltRadio: Required elements are missing in the container.');
        }

        this.inputElement = inputElement;
        this.tileElement = tileElement;

        this.onTileClick = onTileClick;
    }

    setFocus(nextIsFocused: boolean) {
        if (this.isFocused === nextIsFocused) {
            return;
        }

        this.isFocused = nextIsFocused;

        this.tileElement.classList.toggle('ids-alt-radio__tile--focused', nextIsFocused);

        if (nextIsFocused) {
            this.inputElement.focus();
        } else {
            this.inputElement.blur();
        }
    }

    setError(value: boolean) {
        this.tileElement.classList.toggle('ids-alt-radio_tile--error', value);
    }

    getInputElement(): HTMLInputElement {
        return this.inputElement;
    }

    toggleChecked(value?: boolean) {
        const isChecked = value ?? !this.isChecked;

        this.isChecked = isChecked;
        this.inputElement.checked = isChecked;
        this.tileElement.classList.toggle('ids-alt-radio__tile--checked', isChecked);
    }

    initInputListeners() {
        this.inputElement.addEventListener('focus', () => {
            this.setFocus(true);
        });

        this.inputElement.addEventListener('blur', () => {
            this.setFocus(false);
        });

        this.inputElement.addEventListener('input', () => {
            this.toggleChecked();
        });
    }

    initTileBtn() {
        this.tileElement.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            this.inputElement.focus();
            this.inputElement.click();

            this.onTileClick?.(event, this.inputElement.id);
        });
    }

    init() {
        super.init();

        this.initInputListeners();
        this.initTileBtn();
    }
}
