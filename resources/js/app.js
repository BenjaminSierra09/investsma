import {
    browserSupportsWebAuthn,
    startAuthentication,
    startRegistration,
} from "@simplewebauthn/browser";

window.browserSupportsWebAuthn = browserSupportsWebAuthn;
window.startAuthentication = startAuthentication;
window.startRegistration = startRegistration;

import EditorJS from "@editorjs/editorjs";
import Header from "@editorjs/header";
import List from "@editorjs/list";
import Quote from "@editorjs/quote";
import ImageTool from "@editorjs/image";
import Embed from "@editorjs/embed";

let pageEditor;

const parseEditorData = (raw) => {
    if (!raw) return { blocks: [] };

    try {
        const parsed = JSON.parse(raw);
        return parsed && parsed.blocks ? parsed : { blocks: [] };
    } catch (error) {
        console.warn(
            "EditorJS data parse failed, fallback to empty blocks.",
            error
        );
        return { blocks: [] };
    }
};

const destroyEditorIfNeeded = async () => {
    if (!pageEditor) return;

    try {
        await pageEditor.isReady;
        pageEditor.destroy();
    } catch (error) {
        console.warn("EditorJS destroy warning", error);
    }

    pageEditor = undefined;
};

const setupEditor = async () => {
    const holder = document.getElementById("editorjs");
    if (!holder) return;

    const componentId = holder.dataset.editorComponentId;
    const livewireComponent = componentId
        ? window.Livewire?.find(componentId)
        : null;
    const data = parseEditorData(holder.dataset.editorContent);

    await destroyEditorIfNeeded();

    pageEditor = new EditorJS({
        holder,
        data,
        placeholder: "Escribe contenido, agrega encabezados, listas o citas...",
        tools: {
            header: Header,
            list: List,
            quote: Quote,
            embed: Embed,
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: "/api/editorjs/upload", // TODO: backend endpoint para subir archivos
                        byUrl: "/api/editorjs/fetch", // TODO: backend endpoint para subir por URL
                    },
                },
            },
        },
        async onChange() {
            if (!livewireComponent) return;
            const output = await pageEditor.save();
            livewireComponent.set("content", output);
        },
    });

    const saveButton = document.getElementById("save-editor");
    if (saveButton) {
        saveButton.onclick = async () => {
            if (!livewireComponent) return;
            const output = await pageEditor.save();
            livewireComponent.call("saveContent", output);
        };
    }
};

const bootstrapEditor = (event) => {
    if (event?.detail?.content) {
        const holder = document.getElementById("editorjs");
        if (holder) {
            holder.dataset.editorContent = JSON.stringify(event.detail.content);
        }
    }

    queueMicrotask(setupEditor);
};

document.addEventListener("livewire:load", bootstrapEditor);
document.addEventListener("livewire:navigated", bootstrapEditor);
document.addEventListener("page-editor-refreshed", bootstrapEditor);
