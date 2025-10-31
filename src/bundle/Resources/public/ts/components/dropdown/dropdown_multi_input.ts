import { BaseDropdown, BaseDropdownItem } from '../../partials';
import { Chip } from '../chip';
import { OverflowList } from '../overflow_list';
import { createNodesFromTemplate } from '../../utils/dom';

export enum DropdownMultiInputAction {
    Check = 'check',
    Uncheck = 'uncheck',
}

export class DropdownMultiInput extends BaseDropdown {
    private selectionInfoItemsInstance: OverflowList;
    private _sourceInputNode: HTMLSelectElement;
    private _value: string[];

    constructor(container: HTMLDivElement) {
        super(container);

        const _sourceInputNode = this._sourceNode.querySelector<HTMLSelectElement>('select');
        const selectionInfoItemsListNode = this._container.querySelector<HTMLDivElement>('.ids-overflow-list');

        if (!_sourceInputNode || !selectionInfoItemsListNode) {
            throw new Error('DropdownMultiInput: Required elements are missing in the container.');
        }

        this.selectionInfoItemsInstance = new OverflowList(selectionInfoItemsListNode);
        this._sourceInputNode = _sourceInputNode;
        this._value = this.getSelectedValuesFromSource();

        this.onItemClick = this.onItemClick.bind(this);
    }

    protected getSelectedValuesFromSource(): string[] {
        const selectedValues = Array.from(this._sourceInputNode.selectedOptions).map((option) => option.value);

        return selectedValues;
    }

    protected isSelected(id: string): boolean {
        return this._value.includes(id);
    }

    protected setSource() {
        this._sourceInputNode.innerHTML = '';

        this._itemsMap.forEach((item) => {
            const option = document.createElement('option');

            option.value = item.id;
            option.textContent = item.label;

            if (this._value.includes(item.id)) {
                option.selected = true;
            }

            this._sourceInputNode.appendChild(option);
        });

        this.setValues(this.getSelectedValuesFromSource());
    }

    protected setSourceValue(id: string, actionPerformed: DropdownMultiInputAction) {
        const optionNode = this._sourceInputNode.querySelector<HTMLOptionElement>(`option[value="${id}"]`);

        if (!optionNode) {
            return;
        }

        optionNode.selected = actionPerformed === DropdownMultiInputAction.Check;
    }

    protected setSelectedItem(id: string, actionPerformed: DropdownMultiInputAction) {
        const listItemNode = this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${id}"]`);
        const checkboxNode = listItemNode?.querySelector<HTMLInputElement>('.ids-input--checkbox');

        if (!checkboxNode) {
            return;
        }

        checkboxNode.checked = actionPerformed === DropdownMultiInputAction.Check;
    }

    protected setSelectionInfo(values: string[]) {
        const items = values.map((value) => this.getItemById(value)).filter((item): item is BaseDropdownItem => item !== undefined);

        if (items.length) {
            const idsOrder = Array.from(this._itemsMap.keys());
            const selectionItems = items
                .toSorted(({ id: idA }, { id: idB }) => idsOrder.indexOf(idA) - idsOrder.indexOf(idB))
                .map(({ id, label }) => ({ id, label }));

            this._selectionInfoItemsNode.removeAttribute('hidden');
            this._placeholderNode.setAttribute('hidden', '');
            this.selectionInfoItemsInstance.setItems(selectionItems);
            this.initChips();
        } else {
            this._selectionInfoItemsNode.setAttribute('hidden', '');
            this._placeholderNode.removeAttribute('hidden');
            this.selectionInfoItemsInstance.setItems([]);
        }
    }

    protected onChipDelete = (id: string) => {
        this.setValue(id);
    };

    protected initChips() {
        const chipsNodes = this.selectionInfoItemsInstance.getItems();

        chipsNodes.forEach((chipNode) => {
            if (!(chipNode instanceof HTMLDivElement)) {
                return;
            }

            const id = chipNode.dataset.idsId;
            const chipInstance = new Chip(chipNode, {
                onDelete: () => {
                    if (!id) {
                        return;
                    }

                    this.onChipDelete(id);
                },
            });

            chipInstance.init();
        });
    }

    public getItemContent(item: BaseDropdownItem, listItem: HTMLLIElement): NodeListOf<ChildNode> | string {
        const placeholders = {
            '{{ id }}': item.id,
            '{{ label }}': item.label,
        };

        const itemContent = createNodesFromTemplate(listItem.innerHTML, placeholders);

        return itemContent instanceof NodeList ? itemContent : item.label;
    }

    public setItems(items: BaseDropdownItem[]) {
        super.setItems(items);

        const tempValue = this._value;

        this._value = [];

        this.setValues(tempValue);
    }

    public setValues(values: string[]) {
        values.forEach((value) => {
            this.setValue(value);
        });
    }

    public setValue(value: string) {
        const isSelected = this.isSelected(value);
        const nextValue = isSelected ? this._value.filter((iteratedValue) => iteratedValue !== value) : [...this._value, value];
        const actionPerformed = isSelected ? DropdownMultiInputAction.Uncheck : DropdownMultiInputAction.Check;

        this.setSourceValue(value, actionPerformed);
        this.setSelectedItem(value, actionPerformed);
        this.setSelectionInfo(nextValue);

        this._value = nextValue;
    }

    public onItemClick = (event: MouseEvent) => {
        if (event.currentTarget instanceof HTMLLIElement) {
            const { id } = event.currentTarget.dataset;

            if (!id) {
                return;
            }

            this.setValue(id);
        }
    };

    public toggleItemsContainer(nextIsExpanded?: boolean, event?: MouseEvent | KeyboardEvent) {
        if (event?.target instanceof Element && event.target.closest('.ids-chip__delete')) {
            return;
        }

        super.toggleItemsContainer(nextIsExpanded, event);
    }

    public initSelectionInfoItems() {
        this.selectionInfoItemsInstance.init();
        this.initChips();
    }

    public init() {
        super.init();

        this.initSelectionInfoItems();
    }
}
