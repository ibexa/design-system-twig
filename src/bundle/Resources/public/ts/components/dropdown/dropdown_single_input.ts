import { BaseDropdown, BaseDropdownEntry, BaseDropdownItem, flattenDropdownEntries, isDropdownItemGroup } from '../../partials';
import { createNodesFromTemplate } from '../../utils/dom';

export class DropdownSingleInput extends BaseDropdown {
    private _sourceInputNode: HTMLSelectElement;
    private _value?: string;

    constructor(container: HTMLDivElement) {
        super(container);

        const sourceInputNode = this._sourceNode.querySelector<HTMLSelectElement>('select');

        if (!sourceInputNode) {
            throw new Error('DropdownSingleInput: Required elements are missing in the container.');
        }

        this._sourceInputNode = sourceInputNode;

        this._value = this._sourceInputNode.value;

        this.onItemClick = this.onItemClick.bind(this);
    }

    protected syncFromSourceValue() {
        const { value } = this._sourceInputNode;

        this._itemsContainerNode.querySelectorAll<HTMLLIElement>('.ids-dropdown__item--selected').forEach((itemNode) => {
            this.toggleItemSelection(itemNode, false);
        });

        this.toggleItemSelection(this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${value}"]`), true);

        this.setSelectionInfo(value);
        this._value = value;
    }

    protected createOptionNode(item: BaseDropdownItem): HTMLOptionElement {
        const option = document.createElement('option');

        option.value = item.id;
        option.textContent = item.label;
        option.selected = this._value === item.id;

        return option;
    }

    protected setSource() {
        this._sourceInputNode.innerHTML = '';

        this._entries.forEach((entry) => {
            if (!isDropdownItemGroup(entry)) {
                this._sourceInputNode.appendChild(this.createOptionNode(entry));

                return;
            }

            const optgroup = document.createElement('optgroup');

            optgroup.label = entry.label;
            flattenDropdownEntries(entry.items).forEach((item) => {
                optgroup.appendChild(this.createOptionNode(item));
            });
            this._sourceInputNode.appendChild(optgroup);
        });

        this.setValue(this._sourceInputNode.value);
    }

    protected setSourceValue(id: string) {
        this._sourceInputNode.value = id;
    }

    protected dispatchChangeEvent() {
        this._sourceInputNode.dispatchEvent(new Event('change', { bubbles: true }));
    }

    protected setSelectedItem(id: string) {
        const currentId = this._value ?? '';
        const prevSelectedNode = this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${currentId}"]`);
        const nextSelectedNode = this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${id}"]`);

        this.toggleItemSelection(prevSelectedNode, false);
        this.toggleItemSelection(nextSelectedNode, true);
    }

    protected toggleItemSelection(itemNode: HTMLLIElement | null, isSelected: boolean) {
        itemNode?.classList.toggle('ids-dropdown__item--selected', isSelected);
        itemNode?.querySelector('.ids-icon')?.toggleAttribute('hidden', !isSelected);
    }

    public getItemContent(item: BaseDropdownItem, listItem: HTMLLIElement): NodeListOf<ChildNode> | string {
        const placeholders = {
            '{{ id }}': item.id,
            '{{ label }}': item.label,
        };
        const itemContent = createNodesFromTemplate(listItem.innerHTML, placeholders);

        return itemContent instanceof NodeList ? itemContent : item.label;
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

    public setItems(entries: BaseDropdownEntry[]) {
        super.setItems(entries);

        const selectedItem = this.getItemById(this._value);
        const firstItem: BaseDropdownItem | undefined = flattenDropdownEntries(entries)[0];

        if (!selectedItem && firstItem) {
            this.setValue(firstItem.id);
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

    public getSelectedItems(): HTMLOptionElement[] {
        return this._sourceInputNode.selectedIndex === -1 ? [] : [this._sourceInputNode.selectedOptions[0]];
    }

    public selectOption(value: string) {
        this.setValue(value);
        this.dispatchChangeEvent();
    }

    public selectFirstOption() {
        const firstOption = this._sourceInputNode.querySelector<HTMLOptionElement>('option');

        if (!firstOption) {
            return;
        }

        this.selectOption(firstOption.value);
    }

    public clearCurrentSelection() {
        this.selectFirstOption();
    }

    public init() {
        super.init();
        this.syncFromSourceValue();
    }

    public onItemClick = (event: MouseEvent) => {
        if (event.currentTarget instanceof HTMLLIElement) {
            const { id } = event.currentTarget.dataset;

            if (id === undefined) {
                return;
            }

            if (id === this._value) {
                this.toggleItemsContainer(false);

                return;
            }

            this.setValue(id);
            this.dispatchChangeEvent();
            this.toggleItemsContainer(false);
        }
    };
}
