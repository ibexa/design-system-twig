import { AltRadioInput } from './alt_radio_input';
import { BaseInputsList } from '../../partials';

export enum AltRadiosListFieldAction {
    Check = 'check',
    Uncheck = 'uncheck',
}

export class AltRadiosListField extends BaseInputsList<string> {
    private itemsContainer: HTMLDivElement;
    private itemsMap = new Map<string, AltRadioInput>();
    protected value?: string;

    static EVENTS = {
        ...BaseInputsList.EVENTS,
        CHANGE: 'ids:alt-radio-list-field:change',
    };

    constructor(container: HTMLDivElement) {
        super(container);

        const itemsContainer = container.querySelector<HTMLDivElement>('.ids-choice-inputs-list__items');

        if (!itemsContainer) {
            throw new Error('AltRadiosListField: Required elements are missing in the container.');
        }

        this.itemsContainer = itemsContainer;

        this.onItemClick = this.onItemClick.bind(this);

        this.saveItemsInstancesToMap();
    }

    protected saveItemsInstancesToMap() {
        const itemsButtons = this.getItemsButtons();

        this.itemsMap.clear();

        itemsButtons.forEach((button) => {
            const buttonInstance = new AltRadioInput(button, { onTileClick: this.onItemClick });
            const buttonId = buttonInstance.getInputElement().id;

            this.itemsMap.set(buttonId, buttonInstance);
        });
    }

    getItemsButtons() {
        const itemsButtons = [...this.itemsContainer.querySelectorAll<HTMLDivElement>('.ids-alt-radio')];

        return itemsButtons;
    }

    protected onItemClick(_event: MouseEvent, itemValue: string) {
        if (this.value === itemValue) {
            return;
        }

        const changeEvent = new CustomEvent(AltRadiosListField.EVENTS.CHANGE, {
            bubbles: true,
            detail: itemValue,
        });

        if (this.value) {
            const currentValueInstance = this.itemsMap.get(this.value);

            if (currentValueInstance) {
                currentValueInstance.toggleChecked(false);
            }
        }

        this.value = itemValue;
        this._container.dispatchEvent(changeEvent);
    }

    protected initButtons() {
        this.itemsMap.forEach((itemInstance) => {
            itemInstance.init();
        });
    }

    public init() {
        super.init();

        this.initButtons();
    }
}
