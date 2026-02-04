import {
    browserSupportsWebAuthn,
    startAuthentication,
    startRegistration,
} from "@simplewebauthn/browser";
import EditorJS from "@editorjs/editorjs";
import Header from "@editorjs/header";
import List from "@editorjs/list";
import Quote from "@editorjs/quote";
import ImageTool from "editorjs-image-with-link";
import Embed from "@editorjs/embed";
import Warning from "@editorjs/warning";
import Marker from "@editorjs/marker";
import InlineCode from "@editorjs/inline-code";
import Delimiter from "@editorjs/delimiter";
import CodeTool from "@editorjs/code";
import editorjsColumns from "@calumk/editorjs-columns"; // or the other package
import ImageGallery from "@rodrigoodhin/editorjs-image-gallery";
import Table from "@editorjs/table";
import YoutubeEmbed from "editorjs-youtube-embed";
import RawTool from "@editorjs/raw";
import AttachesTool from "@editorjs/attaches";
import Paragraph from "@editorjs/paragraph";
import AlignmentTune from "editor-js-alignment-tune";
import DragDrop from "editorjs-drag-drop";
import Link from "@coolbytes/editorjs-link";

window.browserSupportsWebAuthn = browserSupportsWebAuthn;
window.startAuthentication = startAuthentication;
window.startRegistration = startRegistration;

let pageEditor;
let editorPasteCleanup;

const editorMessages = {
    ui: {
        blockTunes: {
            toggler: {
                "Click to tune": "Haz clic para ajustar",
                "or drag to move": "o arrastra para mover",
            },
        },
        inlineToolbar: {
            converter: {
                "Convert to": "Convertir a",
            },
        },
        toolbar: {
            toolbox: {
                Add: "Agregar",
            },
        },
        popover: {
            Filter: "Buscar",
            "Nothing found": "Sin resultados",
            "Convert to": "Convertir a",
        },
    },
    toolNames: {
        Text: "Párrafo",
        Heading: "Encabezado",
        List: "Lista",
        "Ordered List": "Lista numerada",
        "Unordered List": "Lista con viñetas",
        Quote: "Cita",
        Warning: "Aviso",
        Embed: "Incrustar",
        Table: "Tabla",
        Image: "Imagen",
        Columns: "Columnas",
        "Image Gallery": "Galería de imágenes",
        Code: "Código",
        Delimiter: "Separador",
        Marker: "Resaltador",
        "Inline Code": "Código en línea",
        Bold: "Negrita",
        Italic: "Cursiva",
        YouTube: "YouTube",
        Paragraph: "Párrafo",
        "Raw HTML": "HTML sin procesar",
        Attaches: "Adjuntos",
    },
    tools: {
        header: {
            "Heading 1": "Encabezado 1",
            "Heading 2": "Encabezado 2",
            "Heading 3": "Encabezado 3",
            "Heading 4": "Encabezado 4",
            "Heading 5": "Encabezado 5",
            "Heading 6": "Encabezado 6",
        },
        list: {
            Ordered: "Numerada",
            Unordered: "Viñetas",
        },
        warning: {
            Title: "Título",
            Message: "Mensaje",
        },
        marker: {
            Marker: "Resaltador",
        },
        inlineCode: {
            "Inline code": "Código en línea",
        },
        delimiter: {
            Delimiter: "Separador",
        },
        code: {
            "Enter a code": "Ingresa el código",
        },
        quote: {
            Quote: "Cita",
            Caption: "Autor u origen",
        },
        embed: {
            "Enter a link": "Ingresa un enlace",
            Embed: "Incrustar",
        },
        table: {
            "Add row above": "Agregar fila arriba",
            "Add row below": "Agregar fila abajo",
            "Delete row": "Eliminar fila",
            "Add column to left": "Agregar columna a la izquierda",
            "Add column to right": "Agregar columna a la derecha",
            "Delete column": "Eliminar columna",
            "Delete table": "Eliminar tabla",
        },
        image: {
            Caption: "Pie de foto",
            "Select an Image": "Selecciona una imagen",
            "With border": "Con borde",
            "Stretch image": "Extender imagen",
            "With background": "Con fondo",
        },
        columns: {
            "Add column": "Agregar columna",
            "Delete column": "Eliminar columna",
        },
        imageGallery: {
            "Add Image": "Agregar imagen",
            "Add images": "Agregar imágenes",
            Gallery: "Galería",
        },
        raw: {
            HTML: "HTML",
        },
        attaches: {
            "Select a file": "Selecciona un archivo",
            Attachment: "Adjunto",
            "Select file to upload": "Selecciona un archivo para subir",
        },
        youtubeEmbed: {
            Embed: "Insertar video",
        },
        paragraph: {
            Paragraph: "Párrafo",
        },
    },
    blockTunes: {
        delete: {
            Delete: "Eliminar",
            "Click to delete": "Clic para eliminar",
        },
        moveUp: {
            "Move up": "Mover arriba",
        },
        moveDown: {
            "Move down": "Mover abajo",
        },
    },
};

const parseEditorData = (raw) => {
    if (!raw) return { blocks: [] };

    if (typeof raw === "object") {
        return raw.blocks ? raw : { blocks: [] };
    }

    try {
        const parsed = JSON.parse(raw);
        return parsed && parsed.blocks ? parsed : { blocks: [] };
    } catch (error) {
        console.warn(
            "EditorJS data parse failed, fallback to empty blocks.",
            error,
        );
        return { blocks: [] };
    }
};

const destroyEditorIfNeeded = async () => {
    if (editorPasteCleanup) {
        const { element, handler } = editorPasteCleanup;
        element?.removeEventListener("paste", handler);
        editorPasteCleanup = undefined;
    }

    if (!pageEditor) return;

    try {
        await pageEditor.isReady;
        pageEditor.destroy();
    } catch (error) {
        console.warn("EditorJS destroy warning", error);
    }

    pageEditor = undefined;
};

const extractTableFromHtml = (html) => {
    if (!html) return null;

    const doc = new DOMParser().parseFromString(html, "text/html");
    const table = doc.querySelector("table");
    if (!table) return null;

    const rows = Array.from(table.rows).map((row) =>
        Array.from(row.cells).map((cell) => cell.textContent.trim()),
    );

    if (!rows.length) return null;

    const hasHeadings =
        table.tHead?.rows?.length > 0 ||
        table.querySelectorAll("th").length > 0;

    return { content: rows, withHeadings: Boolean(hasHeadings) };
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

    let column_tools = {
        header: Header,
        alert: Warning,
        quote: Quote,
        marker: Marker,
        inlineCode: InlineCode,
        code: CodeTool,
        embed: Embed,
        youtubeEmbed: YoutubeEmbed,
        raw: RawTool,
        attaches: AttachesTool,
        table: Table,
        paragraph: Paragraph,
        delimiter: Delimiter,
        image: {
            class: ImageTool,
            buttonContent: "Imagen",
            config: {
                uploader: {
                    async uploadByFile(file) {
                        try {
                            const csrfToken = document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content");

                            const formData = new FormData();
                            formData.append("image", file);

                            const response = await fetch("/upload/image", {
                                method: "POST",
                                headers: csrfToken
                                    ? {
                                          "X-CSRF-TOKEN": csrfToken,
                                      }
                                    : undefined,
                                body: formData,
                            });

                            if (!response.ok) {
                                throw new Error("Error al subir la imagen");
                            }

                            const data = await response.json();

                            return {
                                success: 1,
                                file: {
                                    url: data.file.url,
                                },
                            };
                        } catch (error) {
                            console.error("Error:", error);
                            return {
                                success: 0,
                                file: { url: null },
                            };
                        }
                    },
                    async uploadByUrl(url) {
                        try {
                            const csrfToken = document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content");

                            const response = await fetch("/upload/image-url", {
                                method: "POST",
                                headers: {
                                    ...(csrfToken
                                        ? { "X-CSRF-TOKEN": csrfToken }
                                        : {}),
                                    "Content-Type": "application/json",
                                },
                                body: JSON.stringify({ url }),
                            });

                            if (!response.ok) {
                                throw new Error(
                                    "Error al subir la imagen por URL",
                                );
                            }

                            const data = await response.json();

                            return {
                                success: 1,
                                file: {
                                    url: data.file.url,
                                },
                            };
                        } catch (error) {
                            console.error("Error:", error);
                            return {
                                success: 0,
                                file: { url: null },
                            };
                        }
                    },
                },
                captionPlaceholder: "Escribe una leyenda...",
                buttonContent: "Imagen",
                types: "image/*",
            },
        },
        list: List,
    };

    pageEditor = new EditorJS({
        holder,
        data,
        placeholder: "Escribe contenido, agrega encabezados, listas o citas...",
        i18n: {
            messages: editorMessages,
        },
        tools: {
            paragraph: {
                class: Paragraph,
                tunes: ["alignmentTune"],
            },
            header: {
                class: Header,
                tunes: ["alignmentTune"],
            },
            link: Link,
            list: List,
            quote: Quote,
            warning: Warning,
            marker: Marker,
            inlineCode: InlineCode,
            delimiter: Delimiter,
            code: CodeTool,
            embed: Embed,
            youtubeEmbed: {
                class: YoutubeEmbed,
                inlineToolbar: true,
            },
            raw: {
                class: RawTool,
                inlineToolbar: false,
                config: {
                    placeholder:
                        "Pega tu código HTML aquí (por ejemplo, embeds de Instagram, Twitter, etc.)",
                },
            },
            attaches: {
                class: AttachesTool,
                config: {
                    uploader: {
                        async uploadByFile(file) {
                            try {
                                const csrfToken = document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute("content");

                                const formData = new FormData();
                                formData.append("file", file);

                                const response = await fetch("/upload/file", {
                                    method: "POST",
                                    headers: csrfToken
                                        ? {
                                              "X-CSRF-TOKEN": csrfToken,
                                          }
                                        : undefined,
                                    body: formData,
                                });

                                if (!response.ok) {
                                    throw new Error(
                                        "Error al subir el archivo",
                                    );
                                }

                                const data = await response.json();

                                return {
                                    success: 1,
                                    file: {
                                        url: data.file.url,
                                        size: data.file.size,
                                        name: data.file.name,
                                        extension: data.file.extension,
                                    },
                                };
                            } catch (error) {
                                console.error("Error:", error);
                                return {
                                    success: 0,
                                };
                            }
                        },
                    },
                },
            },
            table: Table,
            imageGallery: ImageGallery,
            columns: {
                class: editorjsColumns,
                config: {
                    EditorJsLibrary: EditorJS, // Pass the library instance to the columns instance.
                    tools: column_tools, // IMPORTANT! ref the column_tools
                },
            },

            image: {
                class: ImageTool,
                buttonContent: "Imagen",
                config: {
                    uploader: {
                        async uploadByFile(file) {
                            try {
                                const csrfToken = document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute("content");

                                const formData = new FormData();
                                formData.append("image", file);

                                const response = await fetch("/upload/image", {
                                    method: "POST",
                                    headers: csrfToken
                                        ? {
                                              "X-CSRF-TOKEN": csrfToken,
                                          }
                                        : undefined,
                                    body: formData,
                                });

                                if (!response.ok) {
                                    throw new Error("Error al subir la imagen");
                                }

                                const data = await response.json();

                                return {
                                    success: 1,
                                    file: {
                                        url: data.file.url,
                                    },
                                };
                            } catch (error) {
                                console.error("Error:", error);
                                return {
                                    success: 0,
                                    file: { url: null },
                                };
                            }
                        },
                        async uploadByUrl(url) {
                            try {
                                const csrfToken = document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute("content");

                                const response = await fetch(
                                    "/upload/image-url",
                                    {
                                        method: "POST",
                                        headers: {
                                            ...(csrfToken
                                                ? { "X-CSRF-TOKEN": csrfToken }
                                                : {}),
                                            "Content-Type": "application/json",
                                        },
                                        body: JSON.stringify({ url }),
                                    },
                                );

                                if (!response.ok) {
                                    throw new Error(
                                        "Error al subir la imagen por URL",
                                    );
                                }

                                const data = await response.json();

                                return {
                                    success: 1,
                                    file: {
                                        url: data.file.url,
                                    },
                                };
                            } catch (error) {
                                console.error("Error:", error);
                                return {
                                    success: 0,
                                    file: { url: null },
                                };
                            }
                        },
                    },
                    captionPlaceholder: "Escribe una leyenda...",
                    buttonContent: "Imagen",
                    types: "image/*",
                },
            },
            alignmentTune: {
                class: AlignmentTune,
                config: {
                    default: "left",
                },
            },
        },
        async onChange() {
            if (!livewireComponent) return;
            const output = await pageEditor.save();
            livewireComponent.set("content", output);
        },
        onReady: () => {
            new DragDrop(pageEditor);
        },
    });

    const handleTablePaste = async (event) => {
        const html = event.clipboardData?.getData("text/html");
        const tableData = extractTableFromHtml(html);

        if (!tableData) return;

        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation?.();

        await pageEditor.blocks.insert(
            "table",
            tableData,
            undefined,
            undefined,
            true,
        );

        if (livewireComponent) {
            const output = await pageEditor.save();
            livewireComponent.set("content", output);
        }
    };

    holder.addEventListener("paste", handleTablePaste, true);
    editorPasteCleanup = { element: holder, handler: handleTablePaste };

    const saveButton = document.getElementById("save-editor");
    if (saveButton) {
        saveButton.onclick = async () => {
            if (!livewireComponent) return;
            const output = await pageEditor.save();
            livewireComponent.call("saveContent", output);
        };
    }
};

const scheduleEditorSetup = () => {
    window.requestAnimationFrame(setupEditor);
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", scheduleEditorSetup, {
        once: true,
    });
} else {
    scheduleEditorSetup();
}

document.addEventListener("livewire:load", scheduleEditorSetup);
document.addEventListener("livewire:navigated", scheduleEditorSetup);
