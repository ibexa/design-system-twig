import { setInstance } from '../helpers/object.instances';

export abstract class Base {
    protected _container: HTMLElement;

    static EVENTS = {
        INITIALIZED: 'ids:component:initialized',
    };

    constructor(container: HTMLElement) {
        this._container = container;

        setInstance(container, this);
    }

    getContainer(): HTMLElement {
        return this._container;
    }

    reinit() {
        // to be overridden in subclasses if needed
    }

    init() {
        this._container.setAttribute('data-ids-initialized', 'true');

        this._container.dispatchEvent(new CustomEvent(Base.EVENTS.INITIALIZED, { detail: { component: this } }));
    }
}
