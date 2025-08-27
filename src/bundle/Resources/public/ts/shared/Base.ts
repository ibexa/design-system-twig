import { setInstance } from '../helpers/object.instances';

export default abstract class Base {
    protected _container: HTMLElement;

    static EVENTS = {
        INITIALIZED: 'ids:component:initialized',
    }

    constructor(container: HTMLElement) {
        this._container = container;

        setInstance(container, this);
    }

    get container(): HTMLElement {
        return this._container;
    }

    init() {
        this._container.dispatchEvent(new CustomEvent(Base.EVENTS.INITIALIZED, { detail: { component: this } }));
    }
}
