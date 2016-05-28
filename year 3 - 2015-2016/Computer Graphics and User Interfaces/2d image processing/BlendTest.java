import assignment1.*;

import javax.swing.*;
import java.awt.*;

public class BlendTest extends JFrame {

    public static void main(String[] args) {
        EventQueue.invokeLater(new Runnable() {
            @Override
            public void run() {
                BlendTest m = new BlendTest();
                m.setVisible(true);
            }
        });
    }

    protected void initUI() {
        setTitle("Assignment 1");
        setSize(1000, 800);
        setLocationRelativeTo(null);
        setDefaultCloseOperation(EXIT_ON_CLOSE);
        setLayout(new GridLayout(1, 1));

        assignment1.Image img = new assignment1.Image("flower.png");
        assignment1.Image over = new assignment1.Image("egg.png");

        img.apply(new BlendFilter(over.getOriginal(), 0.5));

        this.add(new JComponent() {
            @Override
            protected void paintComponent(Graphics g) {
                super.paintComponent(g);
                g.drawImage(img.getFiltered(), 0, 0, this);
            }
        });

    }

    public BlendTest() {
        initUI();
    }
}
