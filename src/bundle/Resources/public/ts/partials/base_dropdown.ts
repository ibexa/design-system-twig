import { createPopper } from '@popperjs/core';

import { Base } from './base';
import { Expander } from '../components/expander';
import { InputTextInput } from '../components/input_text';

import { HTMLElementIDSInstance } from '../shared/types';

export interface BaseDropdownItem {
    id: string;
    label: string;
}

export abstract class BaseDropdown extends Base {
    protected _expanderInstance: Expander;
    protected _searchInstance: InputTextInput;
    protected _itemsContainerNode: HTMLDivElement;
    protected _itemsNode: HTMLUListElement;
    protected _placeholderNode: HTMLDivElement;
    protected _searchNode: HTMLDivElement;
    protected _searchWidgetNode: HTMLDivElement;
    protected _selectionInfoNode: HTMLDivElement;
    protected _selectionInfoItemsNode: HTMLDivElement;
    protected _sourceNode: HTMLDivElement;
    protected _widgetNode: HTMLDivElement;
    protected _isExpanded = false;

    constructor(container: HTMLDivElement) {
        super(container);

        const _togglerNode = this._container.querySelector<HTMLElementIDSInstance<Expander>>('.ids-expander');
        const _itemsContainerNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__items-container');
        const _itemsNode = _itemsContainerNode?.querySelector<HTMLUListElement>('.ids-dropdown__items');
        const _selectionInfoNode = this._container.querySelector<HTMLDivElement>(
            '.ids-dropdown__selection-info',
        );
        const _placeholderNode = _selectionInfoNode?.querySelector<HTMLDivElement>('.ids-dropdown__placeholder');
        const _searchNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__search');
        const _searchWidgetNode = _searchNode?.querySelector<HTMLDivElement>('.ids-input-text');
        const _selectionInfoItemsNode = _selectionInfoNode?.querySelector<HTMLDivElement>('.ids-dropdown__selection-info-items');
        const _sourceNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__source');
        const _widgetNode = this._container.querySelector<HTMLDivElement>('.ids-dropdown__widget');


        if (!_togglerNode || !_itemsContainerNode || !_itemsNode || !_placeholderNode || !_searchNode || !_searchWidgetNode || !_selectionInfoItemsNode || !_selectionInfoNode || !_sourceNode || !_widgetNode) {
            throw new Error('Dropdown: Required elements are missing in the container.');
        }

        this._expanderInstance = new Expander(_togglerNode);
        this._searchInstance = new InputTextInput(_searchWidgetNode);
        this._itemsContainerNode = _itemsContainerNode;
        this._itemsNode = _itemsNode;
        this._placeholderNode = _placeholderNode;
        this._searchNode = _searchNode;
        this._searchWidgetNode = _searchWidgetNode;
        this._selectionInfoNode = _selectionInfoNode;
        this._selectionInfoItemsNode = _selectionInfoItemsNode;
        this._sourceNode = _sourceNode;
        this._widgetNode = _widgetNode;

        this.toggleItemsContainer = this.toggleItemsContainer.bind(this);
    }

    clickOutsideItemsContainerHandler = (event: MouseEvent) => {
        if (event.target instanceof Node && !this._widgetNode.contains(event.target) && !this._itemsContainerNode.contains(event.target)) {
            this.toggleItemsContainer(false);
        }
    };

    toggleSearchVisibility(): void {
        const maxVisibleItems = parseInt(this._itemsNode.dataset.maxVisibleItems ?? '10', 10);

        // console.log(this._itemsMap.size);

        console.log(maxVisibleItems);
        // if (isVisible) {
        //     this._searchNode.removeAttribute('hidden');
        // } else {
        //     this._searchNode.setAttribute('hidden', '');
        // }
    }

    isSearchVisible(): boolean {
        return !this._searchNode.hasAttribute('hidden');
    }

    toggleItemsContainer(isExpanded: boolean) {
        this._expanderInstance.toggleIsExpanded(isExpanded);
        this._isExpanded = isExpanded;

        if (isExpanded) {
            const searchInput = this._searchInstance.getInputElement();

            this._itemsContainerNode.style.width = `${this._widgetNode.offsetWidth.toString()}px`;
            this._itemsContainerNode.removeAttribute('hidden');
            searchInput.focus();
            document.addEventListener('click', this.clickOutsideItemsContainerHandler);
        } else {
            this._itemsContainerNode.setAttribute('hidden', '');
            this._searchInstance.changeValue('');
            document.removeEventListener('click', this.clickOutsideItemsContainerHandler);
        }
    }

    initExpander() {
        this._expanderInstance.init();

        this._widgetNode.addEventListener('click', () => {
            this.toggleItemsContainer(!this._isExpanded);
        });
    }

    initItemsContainer() {
        const popperConfig = {
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 4], // eslint-disable-line no-magic-numbers
                    },
                },
            ],
            placement: 'bottom-start' as const,
            strategy: 'fixed' as const,
        };

        createPopper(this._widgetNode, this._itemsContainerNode, popperConfig);
    }

    initSearch() {
        this._searchInstance.init();

        const searchInput = this._searchInstance.getInputElement();

        searchInput.addEventListener('input', () => {
            this.searchItems(searchInput.value);
        });
    }

    init() {
        this.initExpander();
        this.initItemsContainer();
        this.initSearch();

        super.init();
    }

    abstract searchItems(query: string): void;
}
