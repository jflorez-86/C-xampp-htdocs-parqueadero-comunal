
document.addEventListener("DOMContentLoaded", () => {
    const formulario = document.querySelector(".form-grid");

    if (formulario) {
        formulario.addEventListener("submit", (evento) => {
            const placa = formulario.elements["placa"];

            if (placa) {
                placa.value = placa.value.trim().toUpperCase();
            }
        });
    }

    const panel = document.querySelector(".puestos");

    if (panel) {
        const actualizar = async () => {
            try {
                const respuesta = await fetch(
                    "api/disponibilidad.php",
                    { cache: "no-store" }
                );

                if (!respuesta.ok) return;

                const datos = await respuesta.json();

                datos.puestos.forEach((puesto) => {
                    const elemento = [...panel.children].find(
                        (nodo) =>
                            nodo.querySelector("strong")?.textContent ===
                            String(puesto.numero)
                    );

                    if (!elemento) return;

                    const ocupado = puesto.estado === "ocupado";

                    elemento.className = "puesto " +
                        (ocupado ? "ocupado" : "disponible");

                    elemento.querySelector("small").textContent =
                        ocupado ? "Ocupado" : "Libre";
                });
            } catch (error) {
                console.error(
                    "No fue posible actualizar los puestos."
                );
            }
        };

        window.setInterval(actualizar, 15000);
    }
});
