import { setInstance } from '../helpers/object.instances';

export const BASE_EVENTS = {
    INITIALIZED: 'ids:component:initialized',
} as const;

export default abstract class Base {
    protected _container: HTMLElement;

    constructor(container: HTMLElement) {
        this._container = container;

        setInstance(container, this);
    }

    getContainer(): HTMLElement {
        return this._container;
    }

    init() {
        this._container.dispatchEvent(new CustomEvent(BASE_EVENTS.INITIALIZED, { detail: { component: this } }));
    }
}
