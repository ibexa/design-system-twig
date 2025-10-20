import { BaseDropdown, BaseDropdownItem } from '../../partials';

export class DropdownSingleInput extends BaseDropdown {
    private _sourceInputNode: HTMLSelectElement;
    private _value?: string;

    constructor(container: HTMLDivElement) {
        super(container);

        const _sourceInputNode = this._sourceNode.querySelector<HTMLSelectElement>('select');

        if (!_sourceInputNode) {
            throw new Error('DropdownSingleInput: Required elements are missing in the container.');
        }

        this._sourceInputNode = _sourceInputNode;
        this._value = this._sourceInputNode.value;

        this.onItemClick = this.onItemClick.bind(this);
    }

    protected setSource() {
        this._sourceInputNode.innerHTML = '';

        this._itemsMap.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.label;

            if (this._value === item.id) {
                option.selected = true;
            }

            this._sourceInputNode.appendChild(option);
        });

        this.setValue(this._sourceInputNode.value);
    }

    protected setSourceValue(id: string) {
        this._sourceInputNode.value = id;
    }

    protected setSelectedItem(id: string) {
        const currentId = this._value ?? '';
        const prevSelectedNode = this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${currentId}"]`);
        const nextSelectedNode = this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${id}"]`);

        prevSelectedNode?.classList.remove('ids-dropdown__item--selected');
        nextSelectedNode?.classList.add('ids-dropdown__item--selected');
    }

    protected setSelectionInfo(id: string) {
        const item = this.getItemById(id);

        if (item) {
            this._selectionInfoItemsNode.textContent = item.label;
            this._selectionInfoItemsNode.dataset.id = item.id;
            this._selectionInfoItemsNode.removeAttribute('hidden');
            this._placeholderNode.setAttribute('hidden', '');
        } else {
            this._selectionInfoItemsNode.textContent = '';
            this._selectionInfoItemsNode.dataset.id = '';
            this._selectionInfoItemsNode.setAttribute('hidden', '');
            this._placeholderNode.removeAttribute('hidden');
        }
    }

    public setItems(items: BaseDropdownItem[]) {
        super.setItems(items);

        const selectedItem = this.getItemById(this._value);

        if (!selectedItem && items.length > 0) {
            this.setValue(items[0].id);
        }
    }

    public setValue(value: string) {
        if (this._value === value) {
            return;
        }

        this.setSourceValue(value);
        this.setSelectedItem(value);
        this.setSelectionInfo(value);

        this._value = value;
    }

    public onItemClick = (event: MouseEvent) => {
        if (event.currentTarget instanceof HTMLLIElement) {
            const { id } = event.currentTarget.dataset;

            if (!id || id === this._value) {
                return;
            }

            this.setValue(id);
            this.toggleItemsContainer(false);
        }
    };
}
