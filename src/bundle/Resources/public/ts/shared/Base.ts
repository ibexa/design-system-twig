import { setInstance } from '../helpers/object.instances';

export enum BASE_EVENTS {
    INITIALIZED = 'ids:component:initialized',
}

export default abstract class Base {
    private _container: HTMLElement;

    constructor(container: HTMLElement) {
        this._container = container;

        setInstance(container, this);
    }

    get container(): HTMLElement {
        return this._container;
    }

    init() {
        this._container.dispatchEvent(new CustomEvent('ids:component:initialized', { detail: { component: this } }));
    }
}
