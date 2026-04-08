const procesarMateriales = () => {
    const materials = ["Hydrogen", "Helium", "Lithium", "Beryllium"];

            // Obtenemos el arreglo de longitudes: [8, 6, 7, 9]
    const longitudes = materials.map((material) => material.length);

            // Lo imprimimos en el input convirtiéndolo a texto separado por comas
    console.log(materials.map((material) => material.length));
    document.getElementById('resultado').value = longitudes.join(", ");
};