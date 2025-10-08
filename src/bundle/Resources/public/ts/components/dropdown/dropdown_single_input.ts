import { BaseDropdown, BaseDropdownItem } from '../../partials';

interface TemplatesType {
    item?: HTMLTemplateElement;
}

export class DropdownSingleInput extends BaseDropdown {
    private _sourceInputNode: HTMLSelectElement;
    private _templates: TemplatesType = {};
    private _itemsMap = new Map<string, BaseDropdownItem>();
    private _value?: string;

    constructor(container: HTMLDivElement) {
        super(container);

        const _sourceInputNode = this._sourceNode.querySelector<HTMLSelectElement>('select');

        if (!_sourceInputNode) {
            throw new Error('DropdownSingleInput: Required elements are missing in the container.');
        }

        this._sourceInputNode = _sourceInputNode;
        this._templates = {
            item: this._container.querySelector<HTMLTemplateElement>('template.ids-dropdown__item-template') ?? undefined,
        }
        this._value = this._sourceInputNode.value;

        const itemsNodes = this.getItemsNodes();

        this.setItemsMapFromNodes(itemsNodes);
    }

    filterFunction(item: BaseDropdownItem, query: string): boolean {
        return item.label.toLowerCase().includes(query.toLowerCase());
    }

    searchItems(query: string): void {
        const itemsNodes = this.getItemsNodes();

        itemsNodes.forEach((itemNode) => {
            const item = this.getItemById(itemNode.dataset.id ?? '');
            const isVisible = item ? this.filterFunction(item, query) : false;

            if (isVisible) {
                itemNode.removeAttribute('hidden');
            } else {
                itemNode.setAttribute('hidden', '');
            }
        });
    }

    setSourceItems(items: BaseDropdownItem[]) {
        this._sourceInputNode.innerHTML = '';

        items.forEach((item) => {
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

    setItemsContainer(items: BaseDropdownItem[]) {
        const template = this._templates.item?.content.querySelector<HTMLLIElement>('li');

        if (!template) {
            throw new Error('DropdownSingleInput: Item template is missing in the container.');
        }

        this._itemsNode.innerHTML = '';

        items.forEach((item) => {
            const listItem = template.cloneNode(true);

            if (!(listItem instanceof HTMLLIElement)) {
                return;
            }

            listItem.dataset.id = item.id;
            listItem.dataset.label = item.label;
            listItem.textContent = item.label;

            this._itemsNode.appendChild(listItem);

            listItem.addEventListener('click', this.onItemClick);
        });
    }

    setItems(items: BaseDropdownItem[]) {
        this.setItemsMapFromItems(items);
        this.setItemsContainer(items);
        this.setSourceItems(items);
        this.toggleSearchVisibility();
    }

    setItemsMapFromNodes(itemsNodes: HTMLLIElement[]) {
        this._itemsMap.clear();

        itemsNodes.forEach((itemNode) => {
            const item = this.getItemFromNode(itemNode);

            if (item) {
                this._itemsMap.set(item.id, item);
            }
        });
    }

    setItemsMapFromItems(items: BaseDropdownItem[]) {
        this._itemsMap.clear();

        items.forEach((item) => {
            this._itemsMap.set(item.id, item);
        });
    }

    getItemById(id = ''): BaseDropdownItem | undefined {
        return this._itemsMap.get(id);
    }

    setSourceValue(id = '') {
        this._sourceInputNode.value = id;
    }

    setSelectedItem(id = '') {
        const currentId = this._value ?? '';
        const prevSelectedNode = this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${currentId}"]`);
        const nextSelectedNode = this._itemsContainerNode.querySelector<HTMLLIElement>(`.ids-dropdown__item[data-id="${id}"]`);

        prevSelectedNode?.classList.remove('ids-dropdown__item--selected');
        nextSelectedNode?.classList.add('ids-dropdown__item--selected');
    }

    setSelectionInfo(id = '') {
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

    setValue(value: string) {
        if (this._value === value) {
            return;
        }

        this.setSourceValue(value);
        this.setSelectedItem(value);
        this.setSelectionInfo(value);

        this._value = value;
    }

    getItemFromNode(itemNode: HTMLLIElement): BaseDropdownItem | undefined {
        const { id, label } = itemNode.dataset;

        if (!id || !label) {
            return;
        }

        return { id, label };
    }

    onItemClick = (event: MouseEvent) => {
        if (event.currentTarget instanceof HTMLLIElement) {
            const { id } = event.currentTarget.dataset;

            if (!id || id === this._value) {
                return;
            }

            this.setValue(id);
            this.toggleItemsContainer(false);
        }
    }

    getItemsNodes() {
        return [...this._itemsNode.querySelectorAll<HTMLLIElement>('.ids-dropdown__item')];
    }

    initItems() {
        const items = this.getItemsNodes();

        items.forEach((item) => {
            item.addEventListener('click', this.onItemClick);
        });
    }

    init() {
        this.initItems();

        super.init();
    }
}
