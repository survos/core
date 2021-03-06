import { ArrowOverlay } from "./arrow-overlay";
import { JsPlumbInstance } from "../core";
import { Component } from '../component/component';
import { ArrowOverlayOptions, Overlay } from "../overlay/overlay";
export declare class PlainArrowOverlay extends ArrowOverlay {
    instance: JsPlumbInstance;
    static type: string;
    type: string;
    constructor(instance: JsPlumbInstance, component: Component, p: ArrowOverlayOptions);
}
export declare function isPlainArrowOverlay(o: Overlay): o is PlainArrowOverlay;
