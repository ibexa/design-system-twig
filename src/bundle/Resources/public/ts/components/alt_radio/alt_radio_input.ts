import { Base } from '../../partials';

export class AltRadioInput extends Base {
    private _inputElement: HTMLInputElement;
    private _tileElement: HTMLDivElement;

    private _isFocused = false;

    constructor(container: HTMLDivElement) {
        super(container);

        const inputElement = this._container.querySelector<HTMLInputElement>('.ids-alt-radio__source .ids-input');
        const tileElement = this._container.querySelector<HTMLDivElement>('.ids-alt-radio__tile');

        if (!inputElement || !tileElement) {
            throw new Error('AltRadio: Required elements are missing in the container.');
        }

        this._inputElement = inputElement;
        this._tileElement = tileElement;
    }

    setFocus(nextIsFocused: boolean) {
        if (this._isFocused === nextIsFocused) {
            return;
        }

        this._isFocused = nextIsFocused;

        this._tileElement.classList.toggle('ids-alt-radio__tile--focused', nextIsFocused);

        if (nextIsFocused) {
            this._inputElement.focus();
        } else {
            this._inputElement.blur();
        }
    }

    setError(value: boolean) {
        this._tileElement.classList.toggle('ids-alt-radio__tile--error', value);
    }

    getInputElement(): HTMLInputElement {
        return this._inputElement;
    }

    initInputListeners() {
        this._inputElement.addEventListener('focus', () => {
            this.setFocus(true);
        });

        this._inputElement.addEventListener('blur', () => {
            this.setFocus(false);
        });

        this._inputElement.addEventListener('input', () => {
            this._tileElement.classList.toggle('ids-alt-radio__tile--checked', this._inputElement.checked);
        });
    }

    initTileBtn() {
        this._tileElement.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            this._inputElement.focus();
            this._inputElement.click();
        });
    }

    init() {
        super.init();

        this.initInputListeners();
        this.initTileBtn();
    }
}
