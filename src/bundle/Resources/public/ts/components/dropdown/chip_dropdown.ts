import { DropdownSingleInput } from './dropdown_single_input';

export class ChipDropdown extends DropdownSingleInput {
    protected toggleSearchVisibility(): void {} // eslint-disable-line @typescript-eslint/no-empty-function

    public toggleItemsContainer(nextIsExpanded?: boolean) {
        if (this._container.classList.contains('ids-dropdown--disabled')) {
            return;
        }

        super.toggleItemsContainer(nextIsExpanded);

        this._widgetNode.setAttribute('aria-expanded', this._isExpanded.toString());
    }
}
