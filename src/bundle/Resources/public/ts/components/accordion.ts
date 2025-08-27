import Expander, { ExpanderType } from './expander';
import Base from '../shared/Base';

import { HTMLElementIDSInstance } from '../shared/types';

import { reflow } from '../helpers/dom';

export default class Accordion extends Base {
    private _togglerElement: HTMLElementIDSInstance<ExpanderType> | null;
    private _togglerInstance: ExpanderType;
    private _contentElement: HTMLElement | null;

    constructor(container: HTMLElement) {
        super(container);

        this._togglerElement = this._container.querySelector<HTMLElementIDSInstance<ExpanderType>>('.ids-expander');

        if (!this._togglerElement) {
            throw new Error('No toggler element found for this container!');
        }

        this._togglerInstance = new Expander(this._togglerElement);
        this._contentElement = container.querySelector<HTMLElement>('.ids-accordion__content');

        this.onToggleClick = this.onToggleClick.bind(this);
    }

    isExpanded(): boolean {
        return this._container.classList.contains('ids-accordion--is-expanded');
    }

    toggleIsExpanded(isExpanded: boolean) {
        const prevIsExpanded = this._container.classList.contains('ids-accordion--is-expanded');

        if (prevIsExpanded !== isExpanded) {
            this._togglerInstance.toggleIsExpanded(isExpanded);
        }

        if (!this._contentElement) {
            return;
        }

        const initialHeight = isExpanded ? 0 : this._contentElement.offsetHeight;

        this._contentElement.style.height = `${initialHeight.toString()}px`;

        reflow(this._contentElement);

        this._container.classList.toggle('ids-accordion--is-expanded', isExpanded);
        this._container.classList.toggle('ids-accordion--is-animating', true);
        this._contentElement.addEventListener(
            'transitionend',
            () => {
                this._container.classList.toggle('ids-accordion--is-animating', false);

                if (this._contentElement) {
                    this._contentElement.style.removeProperty('height');
                }
            },
            { once: true },
        );

        const finalHeight = isExpanded ? this._contentElement.scrollHeight : 0;

        this._contentElement.style.height = `${finalHeight.toString()}px`;
    }

    onToggleClick(isExpanded: boolean) {
        this.toggleIsExpanded(isExpanded);
    }

    initToggler() {
        this._togglerInstance.init();

        this._togglerInstance.expandHandler = this.onToggleClick.bind(this);
    }

    init() {
        this.initToggler();

        super.init();
    }
}
