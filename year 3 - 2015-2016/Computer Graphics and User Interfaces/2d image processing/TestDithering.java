import assignment1.*;

import javax.swing.*;
import java.awt.*;

public class TestDithering extends JFrame {

    public static void main(String[] args) {
        EventQueue.invokeLater(new Runnable() {
            @Override
            public void run() {
                TestDithering m = new TestDithering();
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

        assignment1.Image img = new assignment1.Image("1.jpg");

        this.add(new JComponent() {
            @Override
            protected void paintComponent(Graphics g) {
                super.paintComponent(g);
                g.drawImage(img.getOriginal(), 0, 0, this);
            }
        });

        img.apply(new Dithering());

        this.add(new JComponent() {
            @Override
            protected void paintComponent(Graphics g) {
                super.paintComponent(g);
                g.drawImage(img.getFiltered(), 0, 0, this);
            }
        });

    }

    public TestDithering() {
        initUI();
    }
}
